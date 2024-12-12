<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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
        $toCheckUser = User::findOrFail($user_id);

        // Check if current user is allowed to create appointment
        if ($currentUser->id === $toCheckUser->id || $currentUser->account_type === 'vets') {

            // Validation rules for input, ensuring only date and hour are considered
            $validator = Validator::make(request()->all(), [
                'pet_id' => 'required|exists:pets,id',
                'start_time' => 'required|date_format:Y-m-d H|after_or_equal:' . Carbon::today()->setHour(8)->setMinute(0)->toDateTimeString(),
                'end_time' => 'nullable|date_format:Y-m-d H|after:start_time',
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


            $endTime = Carbon::parse($validatedData['end_time']);
            if ($endTime->lessThan($startTime)) {
                return response()->json(['message' => 'End time must be after start time.'], 400);
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

        if ($request->appointment_status === 'completed' && $appointment->appointment_status !== 'completed') {
            $appointment->update([
                'appointment_status' => 'completed',
                'end_time' => Carbon::now(),
            ]);
        } else {
            $appointment->update([
                'appointment_status' => $request->appointment_status,
                'end_time' => $request->appointment_status === 'canceled' ? Carbon::today()->setHour(17)->setMinute(0)->setSecond(0) : null,
            ]);
        }

        return response()->json([
            'message' => 'Appointment status updated successfully.',
            'appointment' => $appointment,
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
