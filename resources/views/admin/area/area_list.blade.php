<option value="">Select Area</option>
@foreach($areas AS $area)
    <option value="{{ $area->hashid }}">{{ $area->area_name }}</option>
@endforeach