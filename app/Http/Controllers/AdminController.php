<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Get all settings as a key-value pair.
     */
    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        // Default values if empty
        if ($settings->isEmpty()) {
            $settings = [
                'company_name' => 'FDSMULTSERVICES+',
                'company_address' => 'Maputo, Moçambique',
                'company_phone' => '+258 80 000 0000',
                'company_email' => 'geral@fds.co.mz',
                'company_nuit' => '400000000',
                'enable_notifications' => true,
                'enable_auto_backup' => false,
                'default_currency' => 'MT',
                'tax_rate' => '16',
                'receipt_footer' => 'Obrigado pela sua preferência!',
                'stock_alert_threshold' => '5'
            ];
        }

        return response()->json($settings);
    }

    /**
     * Save settings.
     */
    public function saveSettings(Request $request)
    {
        try {
            $settings = $request->all();
            
            foreach ($request->all() as $key => $value) {
                // Filtra campos que não são configurações
                if (in_array($key, ['_token', 'api_token'])) continue;
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]
                );
            }return response()->json(['message' => 'Configurações salvas com sucesso!']);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao salvar configurações', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create system backup (Placeholder)
     */
    public function createBackup()
    {
        return response()->json(['message' => 'Funcionalidade de backup em desenvolvimento.']);
    }

    /**
     * Get system logs (Placeholder)
     */
    public function getLogs()
    {
        return response()->json(['logs' => 'Histórico de logs indisponível no momento.']);
    }
}
