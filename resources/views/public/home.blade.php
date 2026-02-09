@extends('layouts.public')

@section('title', __('messages.Hotel') . ' - ' . __('messages.Home'))
@section('meta_description', __('messages.Welcome to our hotel') . '. ' . __('messages.Request a Booking') . ' - ' . __('messages.Hotel'))

@push('styles')
<style>
    .hero-public {
        min-height: 85vh;
        background: linear-gradient(135deg, rgba(26,26,26,0.92) 0%, rgba(26,26,26,0.7) 50%), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920') center/cover no-repeat;
        color: #fff;
        display: flex;
        align-items: center;
    }
    .hero-public .section-label { color: var(--hotel-gold); }
    .hero-public .display-title { font-family: 'Cormorant Garamond', serif; font-weight: 600; line-height: 1.15; }
    .room-card { border: none; overflow: hidden; transition: transform 0.25s ease; }
    .room-card:hover { transform: translateY(-4px); }
    .room-card .card-img-wrap { height: 220px; background: #e9ecef center/cover no-repeat; }
    .room-card .price-badge { background: var(--hotel-dark); color: var(--hotel-gold); font-weight: 600; padding: 0.4rem 0.75rem; font-size: 0.9rem; }
    .room-card .card-title { font-family: 'Cormorant Garamond', serif; font-weight: 600; }
    .room-features { font-size: 0.8rem; color: #6c757d; }
    .video-section { min-height: 400px; background: url('https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200') center/cover no-repeat; position: relative; }
    .video-section .overlay { position: absolute; inset: 0; background: rgba(26,26,26,0.4); display: flex; align-items: center; justify-content: center; }
    .video-section .play-btn { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.9); color: var(--hotel-dark); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; transition: transform 0.2s; }
    .video-section .play-btn:hover { transform: scale(1.08); color: var(--hotel-dark); }
    .stat-number { font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 2.5rem; color: var(--hotel-gold); line-height: 1.2; }
    .contact-card { border: none; background: #f8f9fa; padding: 1.5rem; height: 100%; }
    .contact-card .bi { color: var(--hotel-gold); }
</style>
@endpush

@section('content')
    {{-- 1. Hero --}}
    <section class="hero-public">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9">
                    <p class="section-label mb-2">{{ __('messages.Modern luxury and timeless living') }}</p>
                    <h1 class="display-title display-3 mb-4 text-white">{{ __('messages.Welcome to our luxurious hotel & resort') }}</h1>
                    <a class="btn btn-gold rounded-0 btn-lg" href="{{ route('booking.request') }}">{{ __('messages.Book Now') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. About --}}
    <section id="about" class="py-5 py-lg-6">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <p class="section-label mb-2">{{ __('messages.About Us') }}</p>
                    <h2 class="font-serif display-6 fw-semibold mb-4">Since we opened, we've been helping travellers find stays they love — effortlessly.</h2>
                    <p class="text-secondary mb-4">We're about curating unforgettable journeys. {{ __('messages.Hotel') }} combines modern luxury with timeless comfort. Whether for business or leisure, we aim to make every stay memorable.</p>
                    <a href="{{ route('home') }}#rooms" class="text-dark fw-semibold text-decoration-none">{{ __('messages.Know more') }} <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="col-lg-6">
                    <div class="ratio ratio-16x9 rounded overflow-hidden bg-light">
                        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800" alt="{{ __('messages.Hotel') }}" class="object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Stats --}}
    <section class="py-5 bg-light">
        <div class="container">
            <p class="section-label text-center mb-4">{{ __('messages.On the number') }}</p>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <p class="stat-number mb-1">98%+</p>
                    <p class="fw-semibold mb-1">{{ __('messages.Positive feedback') }}</p>
                    <p class="small text-muted mb-0">From our guests</p>
                </div>
                <div class="col-md-4">
                    <p class="stat-number mb-1">15+</p>
                    <p class="fw-semibold mb-1">{{ __('messages.Years of expertise') }}</p>
                    <p class="small text-muted mb-0">In hospitality</p>
                </div>
                <div class="col-md-4">
                    <p class="stat-number mb-1">25K+</p>
                    <p class="fw-semibold mb-1">{{ __('messages.Happy clients') }}</p>
                    <p class="small text-muted mb-0">Worldwide</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Room types (static cards) --}}
    <section id="rooms" class="py-5 py-lg-6">
        <div class="container">
            <p class="section-label text-center mb-1">{{ __('messages.Rooms & Suites') }}</p>
            <h2 class="font-serif display-6 fw-semibold text-center mb-5">{{ __('messages.Our exquisite rooms collections') }}</h2>
            @if($roomTypes->isEmpty())
                <p class="text-center text-muted">{{ __('messages.Room Types') }} — {{ __('messages.None') }}</p>
            @else
                <div class="row g-4">
                    @foreach($roomTypes as $type)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <article class="card room-card h-100 shadow-sm">
                                <div class="card-img-wrap position-relative" style="background-image: url('https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600');">
                                    @if($type->base_rate !== null)
                                        <span class="price-badge position-absolute bottom-0 end-0 m-2">{{ money($type->base_rate, 0) }}/{{ __('messages.per night') }}</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title h5">{{ $type->name }}</h3>
                                    <p class="room-features mb-0">
                                        @if($type->size_sqm){{ $type->size_sqm }} {{ __('messages.m²') }}@endif
                                        @if($type->size_sqm && $type->max_occupancy) &middot; @endif
                                        @if($type->max_occupancy) 1 {{ __('messages.Bed') }} &middot; {{ $type->max_occupancy }} {{ __('messages.Guests') }}@endif
                                    </p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 5. Video / experience --}}
    <section class="video-section">
        <div class="overlay">
            <a href="#" class="play-btn" aria-label="Play video"><i class="bi bi-play-fill"></i></a>
        </div>
    </section>

    {{-- 6. Services & facilities --}}
    <section id="services" class="py-5 py-lg-6 bg-light">
        <div class="container">
            <p class="section-label text-center mb-1">{{ __('messages.Services & Facilities') }}</p>
            <h2 class="font-serif display-6 fw-semibold text-center mb-5">What we offer</h2>
            <div class="row g-4 text-center">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-wifi display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">Wi‑Fi</p></div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-cup-hot display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">Restaurant</p></div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-droplet display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">Pool</p></div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-bicycle display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">Fitness</p></div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-car-front display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">Parking</p></div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-3"><i class="bi bi-headset display-5 text-dark"></i><p class="small mb-0 mt-2 fw-medium">24/7 Front desk</p></div>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Booking CTA --}}
    <section class="py-5">
        <div class="container text-center">
            <h2 class="font-serif display-6 fw-semibold mb-2">{{ __('messages.Request a Booking') }}</h2>
            <p class="text-muted mb-4">Submit your stay details and we will get back to you shortly.</p>
            <a class="btn btn-gold rounded-0 btn-lg" href="{{ route('booking.request') }}">{{ __('messages.Book Now') }}</a>
        </div>
    </section>

    {{-- 8. Contact --}}
    <section id="contact" class="py-5 py-lg-6 bg-light">
        <div class="container">
            <p class="section-label text-center mb-1">{{ __('messages.Contact') }}</p>
            <h2 class="font-serif display-6 fw-semibold text-center mb-5">{{ __('messages.Contact Us') }}</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="contact-card rounded-0 text-center">
                        <i class="bi bi-geo-alt display-6 d-block mb-2"></i>
                        <p class="fw-semibold mb-1">{{ __('messages.Hotel') }}</p>
                        <p class="text-muted small mb-0">123 Hotel Street, City</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="contact-card rounded-0 text-center">
                        <i class="bi bi-telephone display-6 d-block mb-2"></i>
                        <p class="fw-semibold mb-1">{{ __('messages.Contact') }}</p>
                        <p class="text-muted small mb-0">+1 234 567 890</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="contact-card rounded-0 text-center">
                        <i class="bi bi-envelope display-6 d-block mb-2"></i>
                        <p class="fw-semibold mb-1">Email</p>
                        <p class="text-muted small mb-0">info@hotel.example</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
