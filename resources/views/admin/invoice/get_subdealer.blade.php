@forelse ($subdealers as $subdealer)
    <option value="{{ $subdealer->hashid }}">{{ $subdealer->name }} ({{ $subdealer->username }})</option>
@empty
    <option value="">NO SUB DEALERS FOUND</option>    
@endforelse