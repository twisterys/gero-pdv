@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport des dépenses par catégorie')
@push('styles')
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush

@section('page')
    <div class="row">
        <div class="col-12 mb-4 row m-0 justify-content-between">
            <div class="col-md-6 col-12">
                <h2 class="m-0">Rapport des dépenses par catégorie</h2>
            </div>
            <div class="page-title-right col-xl-3 col-lg-4 col-md-5 col-sm-6">
                <form action="{{ route('rapports.categorie-depense') }}" method="get">
                    <div class="input-group border-1 border border-light rounded" id="datepicker1" style="z-index: 9;">
                        <input type="text" class="form-control datepicker text-primary ps-2"
                               id="i_date"
                               placeholder="mm/dd/yyyy"
                               name="i_date"
                               value="{{ request('i_date') }}"
                               readonly>
                        <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des totaux -->
        <div class="col-12">
            <div class="card p-3">
                <div class="row g-3" id="totaux-container">
                    <div class="col-md-4 col-6">
                        <div class="alert alert-primary d-flex align-items-center mb-0">
                            <i class="mdi mdi-cash-multiple fs-2 me-2"></i>
                            <div>
                                <div class="fw-bold">Total TTC</div>
                                <div>{{ number_format($grouped->sum('total_ttc'), 2) }} MAD}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="mdi mdi-cash fs-2 me-2"></i>
                            <div>
                                <div class="fw-bold">Total HT</div>
                                <div>{{ number_format($grouped->sum('total_ht'), 2) }} MAD</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="alert alert-warning d-flex align-items-center mb-0">
                            <i class="mdi mdi-percent fs-2 me-2"></i>
                            <div>
                                <div class="fw-bold">Total TVA</div>
                                <div>{{ number_format($grouped->sum('total_impot'), 2) }} MAD</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4>Détail par catégorie</h4>
                        <hr>
                    </div>
                    <div class="table-responsive d-flex justify-content-center w-100">
                        <table class="table table-bordered table-striped text-center w-100">
                            <thead class="text-center">
                            <tr>
                                <th class="text-center">Catégorie</th>
                                <th class="text-center">Total TTC</th>
                                <th class="text-center">Total HT</th>
                                <th class="text-center">Total TVA</th>
                                <th class="text-center">Total Dépenses</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($grouped as $categorie => $data)
                                <tr>
                                    <td><strong>{{ $categorie }}</strong></td>
                                    <td class="text-center">{{ number_format($data['total_ttc'], 2) }} MAD</td>
                                    <td class="text-center">{{ number_format($data['total_ht'], 2) }} MAD</td>
                                    <td class="text-center">{{ number_format($data['total_impot'], 2) }} MAD</td>
                                    <td class="text-center">{{ $data['nombre_encaisse'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique par catégorie -->
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4>Répartition par catégorie</h4>
                        <hr>
                    </div>
                    <div id="chart-container" class="position-relative">
                        <canvas id="expenseChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('libs/chart.js/Chart.min.css') }}"></script>
    <script src="{{ asset('libs/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('libs/chart.js/Chart.bundle.min.js') }}"></script>

    <script>
        @php
            $exercice = session()->get('exercice');
        @endphp
        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1': ['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{$date_picker_start}}';
        const __datepicker_end_date = '{{$date_picker_end}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';

        $('.datepicker').daterangepicker({
            ranges: __datepicker_dates,
            locale: {
                format: "DD/MM/YYYY",
                separator: " - ",
                applyLabel: "Appliquer",
                cancelLabel: "Annuler",
                fromLabel: "De",
                toLabel: "à",
                customRangeLabel: "Plage personnalisée",
                weekLabel: "S",
                daysOfWeek: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                monthNames: [
                    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
                ],
                firstDay: 1
            },
            startDate: __datepicker_start_date,
            endDate: __datepicker_end_date,
            minDate: __datepicker_min_date,
            maxDate: __datepicker_max_date
        });

        $('#i_date').change(function () {
            $(this).closest('form').submit();
        });

        // Configuration du graphique circulaire
        const chartData = @json($chart_data);
        const categories = Object.keys(chartData);
        const percentages = Object.values(chartData);

        // Couleurs dynamiques pour le graphique
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#FF6B6B', '#4ECDC4',
            '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'
        ];

        // Plugin personnalisé pour afficher les pourcentages
        const centerTextPlugin = {
            id: 'centerText',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data } = chart;
                ctx.save();

                const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);

                data.datasets[0].data.forEach((value, index) => {
                    const meta = chart.getDatasetMeta(0);
                    const arc = meta.data[index];
                    const percentage = ((value / total) * 100).toFixed(1);

                    // Ne pas afficher les pourcentages trop petits
                    if (percentage < 10) return;

                    const centerX = arc.x;
                    const centerY = arc.y;
                    const radius = (arc.innerRadius + arc.outerRadius) / 2;
                    const angle = (arc.startAngle + arc.endAngle) / 2;

                    const x = centerX + Math.cos(angle) * radius * 0.8;
                    const y = centerY + Math.sin(angle) * radius * 0.8;

                    ctx.font = 'bold 14px Arial';
                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    // Ajouter un fond semi-transparent pour améliorer la lisibilité
                    const textWidth = ctx.measureText(percentage + '%').width;
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
                    ctx.fillRect(x - textWidth/2 - 5, y - 8, textWidth + 10, 16);

                    ctx.fillStyle = '#fff';
                    ctx.fillText(percentage + '%', x, y);
                });

                ctx.restore();
            }
        };

        const ctx = document.getElementById('expenseChart').getContext('2d');
        const expenseChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: percentages,
                    backgroundColor: colors.slice(0, categories.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            },
                            // Afficher le pourcentage dans la légende aussi
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].borderColor,
                                            lineWidth: data.datasets[0].borderWidth,
                                            pointStyle: 'circle',
                                            hidden: isNaN(data.datasets[0].data[i]),
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '%';
                            }
                        }
                    }
                },
                cutout: '50%'
            },
            plugins: [centerTextPlugin]
        });
    </script>
@endpush
