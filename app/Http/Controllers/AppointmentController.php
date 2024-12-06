<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function createAppointment(User $user_id)
    {
        $validator = Validator::make(request()->all(),[
            'pet_id' => 'required|exists:pets,id',
            'start_time' => 'required|date|after_or_equal:' . Carbon::today()->setHour(8)->setMinute(0)->toDateTimeString(),
            'end_time' => 'nullable|date|after:start_time',
            'purpose' => 'required|string',
        ]);

        $startTime = Carbon::parse($validator['start_time']);
        if ($startTime->hour < 8 || $startTime->hour >= 17) {
            return response()->json(['message' => 'Appointments can only be scheduled between 8 AM and 5 PM.'], 400);
        }

        if (!$validator['end_time']) {
            $validator['end_time'] = $startTime->copy()->addHour();
        }

        $pet = Pet::findOrFail($validator['pet_id']);

        $appointment = Appointment::create([
            'user_id' => $user_id,
            'pet_id' => $pet->id,
            'start_time' => $validator['start_time'],
            'end_time' => $validator['end_time'],
            'purpose' => $validator['purpose'],
            'appointment_status' => 'booked',
        ]);

        return response()->json([
            'message' => 'Appointment successfully created.',
            'appointment' => $appointment,
        ], 201);
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

    public function showAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        return response()->json([
            'appointment' => $appointment,
        ]);
    }

    public function showAllAppointment(Request $request)
    {
        $appointments = Appointment::all();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }

}