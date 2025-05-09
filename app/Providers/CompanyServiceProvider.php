<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Services\CacheService;
use Carbon\Carbon;

class CompanyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (env('INSTALLATION_STATUS')) {
            $this->app->singleton('company', function () {
                // Verificar si la tabla "company" existe
                if (!Schema::hasTable('company')) {
                    Log::warning('La tabla "company" no existe. Se están utilizando valores predeterminados.');
                    return $this->defaultCompanySettings();
                }else{

                     $company = CacheService::get('company');
                $timezone = $company ? $company->timezone : 'UTC';
                $dateFormat = $company ? $company->date_format : 'Y-m-d';
                $timeFormat = $company ? $company->time_format : 'H:i:s';
                return [
                    'name' => $company->name ?? '',
                    'email' => $company->email ?? '',
                    'timezone' => $timezone,
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                ];
                }

                // Intentar obtener los datos de la tabla "company"
                $company = CacheService::get('company');

                // Si no hay datos disponibles, usar valores predeterminados
                if (!$company) {
                    Log::warning('No se encontró ningún registro en la tabla "company". Se están utilizando valores predeterminados.');
                    return $this->defaultCompanySettings();
                }

                return $this->mapCompanySettings($company);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (env('INSTALLATION_STATUS') && Schema::hasTable('company')) {
            $company = app('company');

            // Configurar la zona horaria
            $timezone = $company['timezone'] ?? 'UTC'; // Fallback a UTC
            if (in_array($timezone, timezone_identifiers_list())) {
                date_default_timezone_set($timezone);
            } else {
                date_default_timezone_set('UTC'); // Fallback si el timezone no es válido
            }

            // Configurar la configuración regional (locale)
            $locale = 'es'; // Cambiar según tus necesidades
            Carbon::setLocale($locale);

            // Configurar los valores de correo solo si están disponibles
            if (!empty($company['email']) && !empty($company['name'])) {
                Config::set('mail.from.address', $company['email']);
                Config::set('mail.from.name', $company['name']);
            }

            // Asegúrate de que no se sobrescriben valores nulos para SMTP
            Config::set('mail.mailers.smtp.host', $company['smtp_host'] ?? env('MAIL_HOST', 'localhost'));
            Config::set('mail.mailers.smtp.port', $company['smtp_port'] ?? env('MAIL_PORT', 1025));
            Config::set('mail.mailers.smtp.username', $company['smtp_username'] ?? env('MAIL_USERNAME', ''));
            Config::set('mail.mailers.smtp.password', $company['smtp_password'] ?? env('MAIL_PASSWORD', ''));
            Config::set('mail.mailers.smtp.encryption', $company['smtp_encryption'] ?? env('MAIL_ENCRYPTION', null));

        }
    }

    /**
     * Valores predeterminados para la configuración de la empresa.
     */
    private function defaultCompanySettings(): array
    {
        return [
            'name' => '',
            'email' => '',
            'mobile' => '',
            'address' => '',
            'tax_number' => '',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'number_precision' => 2,
            'quantity_precision' => 2,
            'show_sku' => false,
            'show_mrp' => false,
            'restrict_to_sell_above_mrp' => false,
            'restrict_to_sell_below_msp' => false,
            'auto_update_sale_price' => false,
            'auto_update_purchase_price' => false,
            'auto_update_average_purchase_price' => false,
            'is_item_name_unique' => false,
            'tax_type' => 'exclusive',
            'enable_serial_tracking' => false,
            'enable_batch_tracking' => false,
            'is_batch_compulsory' => false,
            'enable_mfg_date' => false,
            'enable_exp_date' => false,
            'enable_color' => false,
            'enable_size' => false,
            'enable_model' => false,
            'show_tax_summary' => false,
            'state_id' => null,
            'terms_and_conditions' => '',
            'show_terms_and_conditions_on_invoice' => false,
            'show_party_due_payment' => false,
            'bank_details' => '',
            'signature' => '',
            'show_signature_on_invoice' => false,
            'colored_logo' => '',
            'is_enable_crm' => false,
            'is_enable_carrier' => false,
            'is_enable_carrier_charge' => false,
            'show_discount' => false,
            'allow_negative_stock_billing' => false,
            'show_hsn' => false,
            'is_enable_secondary_currency' => false,
        ];
    }

    /**
     * Mapea los datos de la tabla "company" a un array.
     */
    private function mapCompanySettings($company): array
    {
        return [
            'name' => $company->name ?? '',
            'email' => $company->email ?? '',
            'mobile' => $company->mobile ?? '',
            'address' => $company->address ?? '',
            'tax_number' => $company->tax_number ?? '',
            'timezone' => $company->timezone ?? 'UTC',
            'date_format' => $company->date_format ?? 'Y-m-d',
            'time_format' => $company->time_format ?? 'H:i:s',
            'number_precision' => $company->number_precision ?? 2,
            'quantity_precision' => $company->quantity_precision ?? 2,
            'show_sku' => $company->show_sku ?? false,
            'show_mrp' => $company->show_mrp ?? false,
            'restrict_to_sell_above_mrp' => $company->restrict_to_sell_above_mrp ?? false,
            'restrict_to_sell_below_msp' => $company->restrict_to_sell_below_msp ?? false,
            'auto_update_sale_price' => $company->auto_update_sale_price ?? false,
            'auto_update_purchase_price' => $company->auto_update_purchase_price ?? false,
            'auto_update_average_purchase_price' => $company->auto_update_average_purchase_price ?? false,
            'is_item_name_unique' => $company->is_item_name_unique ?? false,
            'tax_type' => $company->tax_type ?? 'exclusive',
            'enable_serial_tracking' => $company->enable_serial_tracking ?? false,
            'enable_batch_tracking' => $company->enable_batch_tracking ?? false,
            'is_batch_compulsory' => $company->is_batch_compulsory ?? false,
            'enable_mfg_date' => $company->enable_mfg_date ?? false,
            'enable_exp_date' => $company->enable_exp_date ?? false,
            'enable_color' => $company->enable_color ?? false,
            'enable_size' => $company->enable_size ?? false,
            'enable_model' => $company->enable_model ?? false,
            'show_tax_summary' => $company->show_tax_summary ?? false,
            'state_id' => $company->state_id ?? null,
            'terms_and_conditions' => $company->terms_and_conditions ?? '',
            'show_terms_and_conditions_on_invoice' => $company->show_terms_and_conditions_on_invoice ?? false,
            'show_party_due_payment' => $company->show_party_due_payment ?? false,
            'bank_details' => $company->bank_details ?? '',
            'signature' => $company->signature ?? '',
            'show_signature_on_invoice' => $company->show_signature_on_invoice ?? false,
            'colored_logo' => $company->colored_logo ?? '',
            'is_enable_crm' => $company->is_enable_crm ?? false,
            'is_enable_carrier' => $company->is_enable_carrier ?? false,
            'is_enable_carrier_charge' => $company->is_enable_carrier_charge ?? false,
            'show_discount' => $company->show_discount ?? false,
            'allow_negative_stock_billing' => $company->allow_negative_stock_billing ?? false,
            'show_hsn' => $company->show_hsn ?? false,
            'is_enable_secondary_currency' => $company->is_enable_secondary_currency ?? false,
        ];
    }
}