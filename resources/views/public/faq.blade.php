@extends('layouts.public')

@section('content')
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-semibold">{{ __('Pertanyaan yang Sering Diajukan') }}</h1>
            <p class="text-muted">{{ __('Informasi singkat seputar onboarding, pembayaran, dan pengelolaan kontrak di KostIn.') }}</p>
        </div>

        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="false" aria-controls="faqCollapseOne">
                        {{ __('Bagaimana cara mendaftarkan kost?') }}
                    </button>
                </h2>
                <div id="faqCollapseOne" class="accordion-collapse collapse" aria-labelledby="faqHeadingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Daftar sebagai pemilik, lengkapi detail properti di portal owner. Tim admin akan meninjau sebelum publikasi.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                        {{ __('Apakah pembayaran bisa dicicil?') }}
                    </button>
                </h2>
                <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Saat ini pembayaran fokus ke tagihan bulanan dengan Midtrans QRIS. Opsi cicilan dapat dibicarakan langsung dengan pemilik.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeadingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                        {{ __('Bagaimana memantau status kontrak?') }}
                    </button>
                </h2>
                <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        {{ __('Portal tenant menyediakan ringkasan kontrak, jadwal jatuh tempo, dan riwayat pembayaran agar penyewa dapat memantau secara mandiri.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
