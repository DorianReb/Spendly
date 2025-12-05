<div class="col-12 col-md-6">
    <div class="card-soft h-100">
        <div class="meta-card-header mb-2">
            <div>
                <div class="meta-name">
                    <span class="dot-color" style="background:{{ $meta->color_hex ?? '#6C63FF' }}"></span>
                    {{ $meta->nombre }}
                </div>
                <div class="chip-fecha">
                    Objetivo: ${{ number_format($meta->objetivo,2) }}
                    @if($meta->fecha_limite)
                        · Límite {{ \Carbon\Carbon::parse($meta->fecha_limite)->format('d/m/Y') }}
                    @endif
                </div>
            </div>

            <span class="badge-estado badge-{{ $meta->estado }}">
                {{ strtoupper(str_replace('_',' ', $meta->estado)) }}
            </span>
        </div>

        {{-- Progreso --}}
        @php
            $pct = min(100, max(0, $meta->porcentaje));
        @endphp

        <div class="mb-2">
            <div class="d-flex justify-content-between small mb-1">
                <span>Aportado</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar"
                     style="width: {{ $pct }}%;"></div>
            </div>
        </div>

        <div class="meta-footer mt-2">
            <div>
                <span class="fw-semibold">
                    ${{ number_format($meta->total_aportado, 2) }}
                </span>
                de ${{ number_format($meta->objetivo,2) }}
            </div>

            <div class="d-flex gap-1">
                <a href="{{ route('metas.show', $meta) }}"
                   class="btn btn-outline-secondary btn-sm btn-sm-soft">Ver</a>

                <a href="{{ route('metas.edit', $meta) }}"
                   class="btn btn-outline-primary btn-sm btn-sm-soft">Editar</a>

                <form action="{{ route('metas.destroy', $meta) }}"
                      method="POST" class="d-inline form-delete-meta">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="btn btn-outline-danger btn-sm btn-sm-soft btn-delete-meta">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
