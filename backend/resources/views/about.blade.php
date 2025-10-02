@extends('layouts.public')

@section('title', 'About TounsiVert')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-5">About TounsiVert</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <h3 class="text-primary-custom mb-4">Our Mission</h3>
                    <p class="lead">TounsiVert is a community-driven platform dedicated to organizing and supporting impact-driven events across Tunisia.</p>
                    <p>We connect citizens, associations, NGOs, mosques, municipalities, and sponsors to create meaningful change in our communities through:</p>
                    <ul class="mb-0">
                        <li>Food aid and relief donations</li>
                        <li>Healthcare and education initiatives</li>
                        <li>Environmental projects (tree planting, cleanups, recycling)</li>
                        <li>Community support and mosque building</li>
                        <li>Emergency relief efforts</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <h3 class="text-primary-custom mb-4">Why TounsiVert?</h3>
                    <p>Many local initiatives exist but are fragmented across social media. TounsiVert centralizes actions, builds transparency and participation, and measures impact.</p>
                    <p class="mb-0">Our platform enables you to:</p>
                    <ul>
                        <li><strong>Discover</strong> meaningful events in your region</li>
                        <li><strong>Join</strong> onsite actions and make a real difference</li>
                        <li><strong>Support</strong> organizations through donations</li>
                        <li><strong>Share</strong> campaigns and spread awareness</li>
                        <li><strong>Track</strong> your impact through our scoring system</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h3 class="text-primary-custom mb-4">Join Our Community</h3>
                    <p>Whether you're an individual looking to contribute, an organization seeking to expand your reach, or a sponsor wanting to support meaningful causes, TounsiVert is your platform for positive change.</p>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary">Register Now</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
