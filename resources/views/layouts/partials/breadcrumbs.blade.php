@php use App\Helpers\Breadcrumbs; @endphp
@foreach (Breadcrumbs::generate() as $breadcrumb)
    @if (!$loop->last)
        <li class="breadcrumb-item">
            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
        </li>
    @else
        <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
    @endif
@endforeach
