<div class="col-xl-3 col-12 col-md-6 __dashboard_item __dashboard_sortable_item">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <h5 class="text-black-50">@lang($suivi_document['lang'].$suivi_document['type'].'s')</h5>
            </div>
            <div class="card mb-0 overview shadow-none">
                <div class="card-body border-bottom">
                    <div class="">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <div class="overview-content">
                                    <i class="fa fa-file-invoice-dollar  text-success"></i>
                                </div>
                            </div>
                            <div class="col-8 text-end">
                                <p class="text-muted font-size-13 mb-1">Total des @lang($suivi_document['lang'].$suivi_document['type'].'s')</p>
                                <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($suivi_document['total'],3,'.',' ')}}
                                    MAD</h4>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
                <div class="card-body border-bottom">
                    <div class="">
                        <div class="row  align-items-center">
                            <div class="col-4">
                                <div class="overview-content">
                                    <i class="fa fa-cash-register  text-purple"></i>
                                </div>
                            </div>
                            <div class="col-8 text-end">
                                <p class="text-muted font-size-13 mb-1">Recette @lang($suivi_document['lang'].$suivi_document['type'])</p>
                                <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($suivi_document['recette'],3,'.',' ')}}
                                    MAD</h4>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
                <div class="card-body border-bottom">
                    <div class="">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <div class="overview-content">
                                    <i class="fa fa-money-bill-wave text-warning"></i>
                                </div>
                            </div>
                            <div class="col-8 text-end">
                                <p class="text-muted font-size-13 mb-1">Créance @lang($suivi_document['lang'].$suivi_document['type'])</p>
                                <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($suivi_document['creance'],3,'.',' ')}}
                                    MAD</h4>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="">
                        <div class="row  align-items-center">
                            <div class="col-4">
                                <div class="overview-content">
                                    <i class="fa fa-folder text-pink"></i>
                                </div>
                            </div>
                            <div class="col-8 text-end">
                                <p class="text-muted font-size-13 mb-1">Nombre des @lang($suivi_document['lang'].$suivi_document['type'].'s')</p>
                                <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($suivi_document['nombre'],0,'.',' ')}}</h4>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
