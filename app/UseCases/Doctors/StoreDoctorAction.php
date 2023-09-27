<?php

namespace App\UseCases\Doctors;

use App\Models\Doctor;
use App\Helpers\FileHelper;

class StoreDoctorAction
{
    public function __invoke($formData): int
    {
        $doctor = Doctor::create($formData);

        if (request()->has('image')) {
            $fileName = FileHelper::fileMover($formData['image']);
            $doctor->images()->create([
                'url' =>  config('folderName') . '/' . $fileName, // Adjust the path as needed
            ]);
        }
        // Create appointment times based on duty start and end times
        $dutyStartTime = \Carbon\Carbon::parse($formData['duty_start_time']); // Assuming 'duty_start_time' is in a valid time format
        $dutyEndTime = \Carbon\Carbon::parse($formData['duty_end_time']); // Assuming 'duty_end_time' is in a valid time format

        $interval = 30; // 30 minutes interval, you can adjust this as needed
        $appointmentTime = $dutyStartTime->copy();

        while ($appointmentTime <= $dutyEndTime) {
            // Create an appointment entry in the appointment_time table
            $doctor->appointmentTimes()->create([
                'doctor_id' => $doctor->id,
                'appointment_time' => $appointmentTime->format('H:i'), // Format time as 'HH:mm'
            ]);

            // Increment the appointment time by the specified interval
            $appointmentTime->addMinutes($interval);
        }

        return 201;
    }
}
