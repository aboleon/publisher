@if (isset($node['params']['replicable']))
    <div class="d-flex params mt-3">
        <b class="pe-2">Réplicable</b>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="{{ $name }}[params][replicable]" value="1"{{ !empty($node['params']['replicable']) ? ' checked' : ''}}/>
            <label class="form-check-label">Oui</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="{{ $name }}[params][replicable]" value=""{{ empty($node['params']['replicable']) ? ' checked' : ''}}/>
            <label class="form-check-label">Non</label>
        </div>
    </div>
@endif