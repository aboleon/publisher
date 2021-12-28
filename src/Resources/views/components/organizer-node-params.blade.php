<div class="d-flex mt-1 params">
    @if (is_array($type) && array_key_exists('tags', $type))
        @php
            $tags = explode(',', $type['tags']);
        @endphp
        <select name="{{$name}}[params][tag]" class="form-control w-25 me-1">
            <option>-- Tag --</option>
            @foreach($tags as $tag)
                <option{{ isset($node['params']['tag']) && $tag == $node['params']['tag'] ? ' selected' : '' }}>{{ $tag }}</option>
            @endforeach
        </select>
    @endif
    @if (is_array($type) && $type['type'] == 'associated')
            <select name="{{$name}}[params][associated_id]" class="form-control w-50 me-1">
                <option>-- Contenu associé --</option>
                @forelse($associatables as $associated_id => $associated_title)
                    <option value="{{$associated_id}}" {{ $associated_id == $node['params']['associated_id'] ? ' selected' : '' }}>{{ $associated_title }}</option>
                @empty
                @endforelse
            </select>
    @endif
    <input type="text" name="{{$name}}[params][id]" value="{{ $node['params']['id'] }}" class="form-control w-25 me-2" placeholder="id"/>
    <input class="form-control" type="text" name="{{$name}}[params][classes]" value="{{ $node['params']['classes'] ?? '' }}" placeholder="classes"/>
</div>