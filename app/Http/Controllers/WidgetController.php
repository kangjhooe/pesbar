<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\WeatherService;
use App\Services\PrayerTimeService;
use App\Services\EventService;
use App\Services\PollService;
use App\Models\ContactImportant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WidgetController extends BaseApiController
{
    protected $weatherService;
    protected $prayerTimeService;
    protected $eventService;
    protected $pollService;
    
    public function __construct(
        WeatherService $weatherService, 
        PrayerTimeService $prayerTimeService,
        EventService $eventService,
        PollService $pollService
    ) {
        $this->weatherService = $weatherService;
        $this->prayerTimeService = $prayerTimeService;
        $this->eventService = $eventService;
        $this->pollService = $pollService;
    }
    
    /**
     * Get weather data
     */
    public function getWeather(): JsonResponse
    {
        try {
            $weatherData = $this->weatherService->getWeatherData();
            
            return $this->successResponse($weatherData, 'Data cuaca berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Weather API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data cuaca', 500);
        }
    }
    
    /**
     * Get prayer times
     */
    public function getPrayerTimes(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date');
            $prayerData = $this->prayerTimeService->getPrayerTimes($date);
            
            return $this->successResponse($prayerData, 'Data waktu sholat berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Prayer Times API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data waktu sholat', 500);
        }
    }
    
    /**
     * Get next prayer time
     */
    public function getNextPrayer(): JsonResponse
    {
        try {
            $nextPrayer = $this->prayerTimeService->getNextPrayer();
            
            return $this->successResponse($nextPrayer, 'Data sholat berikutnya berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Next Prayer API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data sholat berikutnya', 500);
        }
    }
    
    /**
     * Get contact important data
     */
    public function getContactImportants(): JsonResponse
    {
        try {
            $contacts = ContactImportant::active()->ordered()->get();
            
            return $this->successResponse($contacts, 'Data kontak penting berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Contact Importants API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data kontak penting', 500);
        }
    }
    
    /**
     * Get events data
     */
    public function getEvents(): JsonResponse
    {
        try {
            $eventsData = $this->eventService->getWidgetEvents();
            
            return $this->successResponse($eventsData, 'Data event berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Events API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data event', 500);
        }
    }

    /**
     * Get active poll
     */
    public function getActivePoll(): JsonResponse
    {
        try {
            $pollData = $this->pollService->getActivePoll();
            
            return $this->successResponse($pollData, 'Data polling berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Active Poll API Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data polling', 500);
        }
    }

    /**
     * Submit poll vote
     */
    public function submitPollVote(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'poll_id' => 'required|exists:polls,id',
                'option_ids' => 'required|array',
                'option_ids.*' => 'exists:poll_options,id'
            ]);

            $userId = auth()->id();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            $result = $this->pollService->submitVote(
                $validated['poll_id'],
                $validated['option_ids'],
                $userId,
                $ipAddress,
                $userAgent
            );

            if ($result['success']) {
                return $this->successResponse($result['data'], 'Suara berhasil disimpan');
            }

            return $this->errorResponse($result['message'] ?? 'Gagal menyimpan suara', 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            \Log::error('Submit Poll Vote Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal menyimpan suara', 500);
        }
    }

    /**
     * Get poll results
     */
    public function getPollResults(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'poll_id' => 'required|exists:polls,id'
            ]);

            $results = $this->pollService->getPollResults($validated['poll_id']);
            
            if ($results['success']) {
                return $this->successResponse($results['data'], 'Hasil polling berhasil diambil');
            }

            return $this->errorResponse($results['message'] ?? 'Gagal mengambil hasil polling', 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            \Log::error('Get Poll Results Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil hasil polling', 500);
        }
    }

    /**
     * Get all widget data
     */
    public function getAllWidgets(): JsonResponse
    {
        try {
            $weatherData = $this->weatherService->getWeatherData();
            $prayerData = $this->prayerTimeService->getPrayerTimes();
            $nextPrayer = $this->prayerTimeService->getNextPrayer();
            $contactImportants = ContactImportant::active()->ordered()->get();
            $eventsData = $this->eventService->getWidgetEvents();
            $pollData = $this->pollService->getActivePoll();
            
            return $this->successResponse([
                'weather' => $weatherData,
                'prayer_times' => $prayerData,
                'next_prayer' => $nextPrayer,
                'contact_importants' => $contactImportants,
                'events' => $eventsData,
                'active_poll' => $pollData
            ], 'Data widget berhasil diambil');
        } catch (\Exception $e) {
            \Log::error('Get All Widgets Error: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data widget', 500);
        }
    }

    /**
     * Display events index page
     */
    public function eventsIndex()
    {
        try {
            $eventsData = $this->eventService->getWidgetEvents(20); // Get more events for the index page
            
            return view('events.index', [
                'events' => $eventsData['events'],
                'total_count' => $eventsData['total_count'],
                'updated_at' => $eventsData['updated_at'] ?? null
            ]);
        } catch (\Exception $e) {
            return view('events.index', [
                'events' => collect(),
                'total_count' => 0,
                'updated_at' => null
            ]);
        }
    }
}
