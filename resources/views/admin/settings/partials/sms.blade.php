@php use Illuminate\Support\Facades\Crypt; @endphp

<div class="wg-box">
    <h4 class="mb-4">SMS Settings</h4>

    <form action="{{ adminRoute('admin.settings.sms.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="label">Select SMS Provider</label>
            <select name="provider" id="provider" x-data x-init="$el.value='{{ old('provider', $smsSetting->provider ?? '') }}'" x-on:change="$dispatch('provider-changed', $event.target.value)"
                class="form-control">
                <option value="">-- Select Provider --</option>
                <option value="advanta">Advanta SMS</option>
                <option value="africas_talking">Africa's Talking</option>
                <option value="ajookatt">AjooKatt</option>
                <option value="beem">Beem SMS</option>
                <option value="blessed_texts">Blessed Texts</option>
            </select>
        </div>

        <div x-data="smsProviderFields()" x-init="init()" @provider-changed.window="onProviderChanged($event.detail)">

            <!-- Advanta -->
            <div x-show="provider === 'advanta'" class="space-y-4">
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($smsSetting) && $smsSetting->provider === 'advanta' ? (function(){ try{ return Crypt::decryptString($smsSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Partner ID</label>
                    <input type="text" name="partner_id" value="{{ old('partner_id') ?: ($smsSetting->partner_id ?? '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id') ?: ($smsSetting->sender_id ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Africa's Talking -->
            <div x-show="provider === 'africas_talking'" class="space-y-4">
                <div>
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username') ?: (isset($smsSetting) && $smsSetting->provider === 'africas_talking' ? (function(){ try{ return Crypt::decryptString($smsSetting->username); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($smsSetting) && $smsSetting->provider === 'africas_talking' ? (function(){ try{ return Crypt::decryptString($smsSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id') ?: ($smsSetting->sender_id ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Ajookatt -->
            <div x-show="provider === 'ajookatt'" class="space-y-4">
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($smsSetting) && $smsSetting->provider === 'ajookatt' ? (function(){ try{ return Crypt::decryptString($smsSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id') ?: ($smsSetting->sender_id ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Beem SMS -->
            <div x-show="provider === 'beem'" class="space-y-4">
                <div>
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username') ?: (isset($smsSetting) && $smsSetting->provider === 'beem' ? (function(){ try{ return Crypt::decryptString($smsSetting->username); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key') ?: (isset($smsSetting) && $smsSetting->provider === 'beem' ? (function(){ try{ return Crypt::decryptString($smsSetting->api_key); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id') ?: ($smsSetting->sender_id ?? '') }}" class="form-control">
                </div>
            </div>

            <!-- Blessed Texts -->
            <div x-show="provider === 'blessed_texts'" class="space-y-4">
                <div>
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username') ?: (isset($smsSetting) && $smsSetting->provider === 'blessed_texts' ? (function(){ try{ return Crypt::decryptString($smsSetting->username); } catch(\Exception $e){ return ''; } })() : '') }}" class="form-control">
                </div>
                <div>
                    <label class="label">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id') ?: ($smsSetting->sender_id ?? '') }}" class="form-control">
                </div>
            </div>

        </div>

        <div class="mt-6 text-right">
            <button class="tf-button style-1 w208" type="submit">Save SMS Settings</button>
        </div>
    </form>
</div>

<script>
    function smsProviderFields() {
        return {
            provider: '{{ old('provider', $smsSetting->provider ?? '') }}',
            init() {
                // initialize provider from select if available
                const sel = document.getElementById('provider');
                if (sel && sel.value) this.provider = sel.value;
            },
            onProviderChanged(value) {
                this.provider = value;
                // clear inputs not relevant? Not necessary; server-side validation will enforce
            }
        }
    }
</script>
