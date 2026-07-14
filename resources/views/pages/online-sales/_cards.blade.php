<div class="mb-3 row">

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>
                    Rp {{ number_format($statistics['marketplace_total'], 0, ',', '.') }}
                </h3>

                <p>Total Penjualan</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill-wave"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>
                    Rp {{ number_format($statistics['received'], 0, ',', '.') }}
                </h3>

                <p>Dana Diterima</p>

            </div>

            <div class="icon">

                <i class="fas fa-wallet"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>
                    Rp {{ number_format($statistics['sales'], 0, ',', '.') }}
                </h3>

                <p>Total Nota</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill-wave"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($statistics['received'] - $statistics['sales']) }}</h3>

                <p>Total Kembalian</p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>
    <div class="col-lg-2 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($statistics['marketplace_total'] - $statistics['received']) }}</h3>

                <p>Total Admin</p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($statistics['orders']) }}</h3>

                <p>Total Order</p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($statistics['waiting_settlement']) }}</h3>

                <p>Belum Settlement</p>

            </div>

            <div class="icon">

                <i class="fas fa-clock"></i>

            </div>

        </div>

    </div>

</div>
