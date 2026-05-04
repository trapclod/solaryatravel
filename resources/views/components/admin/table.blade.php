@props([
    'headers' => [],
    'striped' => false,
    'hover' => true,
])

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table {{ $attributes->merge(['class' => 'table mb-0 align-middle' . ($striped ? ' table-striped' : '') . ($hover ? ' table-hover' : '')]) }}>
            @if(count($headers) > 0)
                <thead class="table-light">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="text-uppercase small text-muted fw-semibold py-3" style="letter-spacing:.05em">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @elseif(isset($head))
                <thead class="table-light">{{ $head }}</thead>
            @endif

            <tbody>{{ $slot }}</tbody>

            @if(isset($foot))
                <tfoot class="table-light">{{ $foot }}</tfoot>
            @endif
        </table>
    </div>
</div>
