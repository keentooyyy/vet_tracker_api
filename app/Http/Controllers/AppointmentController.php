<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function createAppointment(User $user_id)
    {
        $currentUser = Auth::user();
        $toCheckUser = User::get()->findOrFail($user_id);

        // Check if current user is allowed to create appointment
        if ($currentUser->id === $toCheckUser->id || $currentUser->account_type === 'vets') {

            // Validation rules for input, ensuring only date and hour are considered
            $validator = Validator::make(request()->all(), [
                'pet_id' => 'required|exists:pets,id',
                'start_time' => 'required|date|after_or_equal:' . Carbon::today()->setHour(8)->setMinute(0)->toDateTimeString(),
                'end_time' => 'nullable|date|after:start_time',
                'purpose' => 'required|string',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Get validated data
            $validatedData = $validator->validated();

            // Ensure start_time is between 8 AM and 5 PM (hours only)
            $startTime = Carbon::parse($validatedData['start_time']);
            if ($startTime->hour < 8 || $startTime->hour >= 17) {
                return response()->json(['message' => 'Appointments can only be scheduled between 8 AM and 5 PM.'], 400);
            }

            // If end_time is not provided, set it one hour after start_time
            if (empty($validatedData['end_time'])) {
                $validatedData['end_time'] = $startTime->copy()->addHour()->format('Y-m-d H');
            } else {
                $endTime = Carbon::parse($validatedData['end_time']);
                if ($endTime->lessThan($startTime)) {
                    return response()->json(['message' => 'End time must be after start time.'], 400);
                }
            }

            // Find the pet based on pet_id
            $pet = Pet::findOrFail($validatedData['pet_id']);

            // Create the appointment
            $appointment = Appointment::create([
                'user_id' => $user_id->id,
                'pet_id' => $pet->id,
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'purpose' => $validatedData['purpose'],
                'appointment_status' => 'booked',
            ]);

            $vet = User::where('account_type' , 'vets')->get()->first();

             Notification::create([
                'user_id' => $vet->id,
                'title' => 'Appointment Booked',
                'appointment_id' => $appointment->id,
                'message' => $user_id->first_name . ' created an appointment for his pet ' . $pet->name . '.',
            ]);


            // Return the created appointment
            return response()->json([
                'appointment' => $appointment,
            ], 201);
        }

        // If unauthorized, return an error
        return response()->json([
            'message' => 'Unauthorized',
        ], 403);
    }


    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        $request->validate(['appointment_status' => 'required|in:booked,canceled,completed']);

        $appointment = Appointment::findOrFail($appointmentId);

        $appointment->update([
            'appointment_status' => $request->appointment_status
        ]);

        $send_to_user_id = $appointment->user_id;

// Get the pet relationship and pet's name
        $pet = $appointment->pet; // Assuming Appointment has a 'pet' relationship
        $pet_name = $pet ? $pet->name : 'Unknown pet'; // Handle cases where pet is not found

// Determine the status message
        $status_message = match ($request->appointment_status) {
            'completed' => 'approved',
            'canceled' => 'canceled',
            default => $request->appointment_status, // For any other statuses
        };

// Create the notification
        Notification::create([
            'user_id' => $send_to_user_id,
            'title' => 'Appointment ' . ucfirst($status_message),
            'appointment_id' => $appointmentId,
            'message' => 'The vet has marked the appointment for ' . $pet_name . ' as ' . $status_message,
        ]);


        return response()->json([
            'message' => 'Appointment status updated successfully.',
        ]);
    }

    public function cancelPastAppointments()
    {
        $now = Carbon::now();
        $todayEndOfDay = Carbon::today()->setTime(17, 0, 0);


        if ($now->gt($todayEndOfDay)) {
            $appointments = Appointment::whereDate('start_time', $now->toDateString())
                ->where('appointment_status', 'booked')
                ->get();

            if ($appointments->isEmpty()) {
                return response()->json(['message' => 'No booked appointments to cancel for today.']);
            }

            foreach ($appointments as $appointment) {
                $appointment->update([
                    'appointment_status' => 'canceled',
                    'end_time' => $todayEndOfDay,
                ]);
            }

            return response()->json(['message' => 'Booked appointments for today have been canceled after 5 PM.']);
        }

        return response()->json(['message' => 'It is not past 5 PM yet. No appointments canceled.']);
    }

//    public function showAppointment($appointmentId)
//    {
//        $appointment = Appointment::findOrFail($appointmentId);
//
//        return response()->json([
//            'appointment' => $appointment,
//        ]);
//    }

    public function showUserAppointments(User $user_id)
    {
        $appointments = Appointment::where('user_id', $user_id->id)
            ->where('appointment_status', 'booked')
            ->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }

    public function showAllAppointment()
    {
        // Fetch all appointments with status "booked"
        $appointments = Appointment::where('appointment_status', 'booked')
            ->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }


}
