@extends('layouts.public-react')

@section('content')
    @php
        $landingProps = [
            'routes' => [
                'home' => route('home'),
                'faq' => route('faq'),
                'about' => route('about'),
                'login' => route('login'),
                'register' => route('register'),
            ],
            'properties' => $properties
                ->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'name' => $property->name,
                        'address' => $property->address,
                        'status' => $property->status,
                        'photos' => $property->photos ?? [],
                        'room_types' => $property->roomTypes
                            ->map(fn ($roomType) => [
                                'id' => $roomType->id,
                                'name' => $roomType->name,
                                'base_price' => $roomType->base_price,
                            ])
                            ->values(),
                        'owner' => $property->owner
                            ? [
                                'name' => $property->owner->name,
                                'email' => $property->owner->email,
                            ]
                            : null,
                    ];
                })
                ->values(),
        ];
    @endphp

    <div id="landing-root" data-props='@json($landingProps)'></div>
@endsection
