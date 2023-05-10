@forelse($subareas AS $subarea)
    <option value="{{ $subarea->hashid }}">{{ $subarea->area_name }}</option>
@empty
<option value="">No subareas found</option>
@endforelse