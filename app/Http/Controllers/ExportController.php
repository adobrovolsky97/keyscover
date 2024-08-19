<?php

namespace App\Http\Controllers;

use App\Http\Requests\Export\ExportRequest;
use App\Http\Resources\Export\ExportResource;
use App\Jobs\ExportJob;
use App\Models\Export\Export;
use App\Services\Export\Contracts\ExportServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ExportController
 */
class ExportController extends Controller
{
    /**
     * @var ExportServiceInterface
     */
    private ExportServiceInterface $exportService;

    /**
     * @param ExportServiceInterface $exportService
     */
    public function __construct(ExportServiceInterface $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ExportResource::collection($this->exportService->getAllPaginated());
    }

    /**
     * @param ExportRequest $request
     * @return JsonResponse
     */
    public function export(ExportRequest $request): JsonResponse
    {
        $exportModel = $this->exportService->create($request->validated());

        ExportJob::dispatch($exportModel);

        return response()->json();
    }

    /**
     * Download the export file
     *
     * @param Export $export
     * @return BinaryFileResponse
     */
    public function download(Export $export): BinaryFileResponse
    {
        return response()->download(Storage::disk('public')->path($export->file_path));
    }
}
