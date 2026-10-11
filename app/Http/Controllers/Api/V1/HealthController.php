<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @group Sistema
 *
 * Pontos de extremidade para monitorizar o Ulimbo ERP Gateway.
 */
class HealthController extends Controller
{
    /**
     * Verificar integridade do gateway
     *
     * Verifica se o Ulimbo ERP Gateway está a responder
     * e devolve informações básicas sobre o serviço.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "service": "ERPGateway",
     *     "status": "healthy",
     *     "version": "1.0.0",
     *     "uptime": "operational"
     *   },
     *   "meta": {
     *     "request_id": "ULIMBO-TEST-001",
     *     "timestamp": "2026-10-11T02:00:00+00:00",
     *     "version": "v1"
     *   }
     * }
     */
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success([
            'service' => 'ERPGateway',
            'status' => 'healthy',
            'version' => '1.0.0',
            'uptime' => 'operational',
        ]);
    }
}