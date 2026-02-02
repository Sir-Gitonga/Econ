@php use Illuminate\Support\Facades\Crypt; @endphp

<div class="wg-box">
    <h4 class="mb-4">WhatsApp Settings</h4>

    <form action="{{ adminRoute('admin.settings.whatsapp.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="label">Select WhatsApp Gateway</label>
            <select name="gateway" id="gateway" x-data x-init="$el.value='{{ old('gateway', $whatsappSetting->gateway ?? '') }}'" x-on:change="$dispatch('gateway-changed', $event.target.value)"
                class="form-control">
                <option value="">-- Select Gateway --</option>
                <option value="apiwap">APIWAP</option>
                <option value="infobip">Infobip</option>
                <option value="twilio">Twilio</option>
            </select>
        </div>

        <div x-data="whatsappGatewayFields()" x-init="init()" @gateway-changed.window="onGatewayChanged($event.detail)">

            <!-- APIWAP -->
            <div x-show="gateway === 'apiwap'" class="space-y-4">
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($whatsappSetting) && $whatsappSetting->gateway === 'apiwap' ? (function(){ try{ return Crypt::decryptString($whatsappSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Instance ID</label>
                    <input type="text" name="instance_id" value="{{ old('instance_id') ?: ($whatsappSetting->instance_id ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Infobip -->
            <div x-show="gateway === 'infobip'" class="space-y-4">
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($whatsappSetting) && $whatsappSetting->gateway === 'infobip' ? (function(){ try{ return Crypt::decryptString($whatsappSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Base URL</label>
                    <input type="text" name="base_url" value="{{ old('base_url') ?: ($whatsappSetting->base_url ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Twilio -->
            <div x-show="gateway === 'twilio'" class="space-y-4">
                <div>
                    <label class="label">Account SID</label>
                    <input type="text" name="account_sid" value="{{ old('account_sid') ?: (isset($whatsappSetting) && $whatsappSetting->gateway === 'twilio' ? (function(){ try{ return Crypt::decryptString($whatsappSetting->account_sid); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Auth Token</label>
                    <input type="text" name="auth_token" value="{{ old('auth_token') ?: (isset($whatsappSetting) && $whatsappSetting->gateway === 'twilio' ? (function(){ try{ return Crypt::decryptString($whatsappSetting->auth_token); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">From Number</label>
                    <input type="text" name="from_number" value="{{ old('from_number') ?: ($whatsappSetting->from_number ?? '') }}" class="form-control">
                </div>
            </div>

        </div>

        <div class="mt-6 text-right">
            <button class="tf-button style-1 w208" type="submit">Save WhatsApp Settings</button>
        </div>
    </form>
</div>

<script>
    function whatsappGatewayFields() {
        return {
            gateway: '{{ old('gateway', $whatsappSetting->gateway ?? '') }}',
            init() {
                const sel = document.getElementById('gateway');
                if (sel && sel.value) this.gateway = sel.value;
            },
            onGatewayChanged(value) {
                this.gateway = value;
            }
        }
    }
</script>
