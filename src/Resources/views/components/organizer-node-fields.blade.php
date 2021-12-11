<input type="hidden" name="{{$name}}[type]" value="{{ $node['type'] }}"/>
<input type="hidden" name="{{$name}}[position]" class="order" value="{{ $node['position'] }}">
<input type="hidden" name="{{$name}}[id]" value="{{ $node['id'] }}"/>
<input type="hidden" name="{{$name}}[is_deleted]" class="is_deleted" value="{{ (bool)$node['deleted_at'] }}"/>