@extends('layouts.auth')

@section('title', 'Terms and Conditions | Padayon Massage Center')

@section('content')
    <div class="mx-auto max-w-4xl">

        <a href="{{ route('register') }}"
            class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-[#849753] hover:underline">
            &larr; Back to Registration
        </a>

        <article class="rounded-2xl bg-white p-6 shadow-lg sm:p-10">

            <div class="mb-8 border-b border-gray-200 pb-6">
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-[#849753]">
                    Padayon Massage Center
                </p>

                <h1 class="text-3xl font-bold text-[#6F4E37]">
                    Terms and Conditions
                </h1>

                <p class="mt-3 text-sm text-gray-500">
                    Last Updated: November 10, 2026
                </p>
            </div>

            <div class="space-y-7 text-sm leading-7 text-gray-600">

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">1. Introduction</h2>
                    <p>
                        Welcome to Padayon Massage Center, powered by
                        DiverseCare Wellness Hub.
                    </p>
                    <p class="mt-2">
                        These Terms and Conditions govern your access to and use
                        of our website, customer accounts, appointment booking
                        system, and massage services.
                    </p>
                    <p class="mt-2">
                        By accessing our website, creating an account, or using
                        our services, you agree to comply with these Terms and
                        Conditions.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">2. Account Registration</h2>
                    <p>
                        Customers may create an account through our website
                        to access available customer services and appointment
                        management features.
                    </p>
                    <p class="mt-2">When registering, you agree to:</p>
                    <ul class="ml-5 list-disc">
                        <li>Provide accurate and complete registration information.</li>
                        <li>Use your own valid email address and contact number.</li>
                        <li>Keep your login credentials confidential.</li>
                        <li>Notify us if you become aware of unauthorized account access.</li>
                        <li>Keep your account information updated when necessary.</li>
                    </ul>
                    <p class="mt-2">
                        You are responsible for activities performed through
                        your account, subject to applicable law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">3. Account Verification and Access</h2>
                    <p>
                        Certain account features may require email verification
                        or other verification procedures.
                    </p>
                    <p class="mt-2">
                        We reserve the right to restrict or suspend access to
                        accounts involved in fraudulent activity, unauthorized
                        access, misuse of our website, or violations of these
                        Terms, subject to applicable law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">4. Appointment Bookings</h2>
                    <p>
                        Customers may use our website to view available services,
                        select therapists, and request appointment schedules.
                    </p>
                    <p class="mt-2">
                        Submitting an appointment request does not automatically
                        guarantee confirmation. Appointment availability and
                        confirmation are subject to our booking procedures.
                    </p>
                    <p class="mt-2">
                        Customers are responsible for providing accurate
                        appointment information and reviewing their booking
                        details before submission.
                    </p>
                    <p class="mt-2">
                        Additional booking, rescheduling, and no-show rules
                        are explained in our Appointment and Booking Policy.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">5. Payments</h2>
                    <p>
                        Customers may be required to make full or partial
                        payments depending on the available payment options
                        and appointment requirements.
                    </p>
                    <p class="mt-2">
                        Online payment transactions must be completed through
                        the payment methods provided by our website.
                    </p>
                    <p class="mt-2">
                        Payment verification and appointment payment status
                        are subject to our payment processing procedures.
                    </p>
                    <p class="mt-2">
                        Refunds, cancellations, and outstanding balances are
                        governed by our Payment, Refund and Cancellation Policy.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">6. Customer Responsibilities</h2>
                    <p>By using our services, you agree to:</p>
                    <ul class="ml-5 list-disc">
                        <li>Provide truthful and accurate information.</li>
                        <li>Respect our employees, massage therapists, and other customers.</li>
                        <li>Follow applicable appointment and service procedures.</li>
                        <li>Disclose relevant health concerns before receiving massage services.</li>
                        <li>Use our website only for lawful and legitimate purposes.</li>
                        <li>Avoid fraudulent transactions or misuse of our booking system.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">7. Health and Wellness Disclaimer</h2>
                    <p>
                        Massage services provided by Padayon Massage Center
                        are intended for general wellness, relaxation, and
                        comfort.
                    </p>
                    <p class="mt-2">
                        Our massage services are not a substitute for medical
                        diagnosis, medical treatment, or professional healthcare
                        advice.
                    </p>
                    <p class="mt-2">
                        Customers should disclose relevant health conditions,
                        previous operations, injuries, and physical concerns
                        before receiving services. Customers should consult
                        an appropriate healthcare professional when necessary.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">8. Respectful and Appropriate Conduct</h2>
                    <p>
                        Padayon Massage Center promotes a safe, respectful,
                        and professional environment for customers, employees,
                        and massage therapists.
                    </p>
                    <p class="mt-2">
                        Harassment, discrimination, threats, inappropriate
                        behavior, and any form of misconduct toward our
                        personnel or other customers will not be tolerated.
                    </p>
                    <p class="mt-2">
                        We reserve the right to refuse or discontinue services
                        when necessary to protect the safety and dignity of
                        our customers and personnel, subject to applicable law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">9. Privacy and Personal Information</h2>
                    <p>
                        Your use of our website and services is also governed
                        by our Privacy Policy.
                    </p>
                    <p class="mt-2">
                        We collect and process personal information for
                        legitimate purposes such as account management,
                        appointment processing, payment verification, and
                        customer support.
                    </p>
                    <p class="mt-2">
                        Please review our
                        <a href="{{ route('privacy-policy') }}" class="font-semibold text-[#849753] hover:underline">
                            Privacy Policy
                        </a>
                        to understand how your information is handled.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">10. Website Use and Security</h2>
                    <p>
                        You agree not to:
                    </p>
                    <ul class="ml-5 list-disc">
                        <li>Attempt unauthorized access to our website or systems.</li>
                        <li>Interfere with website operations or security.</li>
                        <li>Submit false or misleading information.</li>
                        <li>Use our services for fraudulent or unlawful activities.</li>
                        <li>Exploit website errors or vulnerabilities.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">11. Service Availability and Changes</h2>
                    <p>
                        Available services, therapists, appointment schedules,
                        and website features may change depending on operational
                        requirements and availability.
                    </p>
                    <p class="mt-2">
                        We will make reasonable efforts to maintain access to
                        our website and services. However, temporary interruptions
                        may occur due to maintenance, technical problems, or
                        circumstances beyond our control.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">12. Intellectual Property</h2>
                    <p>
                        Website content, branding, logos, text, images, and
                        other materials belonging to Padayon Massage Center
                        may not be copied, reproduced, distributed, or used
                        without appropriate authorization, except as permitted
                        by applicable law.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">13. Changes to These Terms</h2>
                    <p>
                        Padayon Massage Center may revise these Terms and
                        Conditions when necessary due to changes in services,
                        operations, or applicable laws.
                    </p>
                    <p class="mt-2">
                        Updated versions will be published on this page with
                        the corresponding revision date.
                    </p>
                    <p class="mt-2">
                        Where required, customers may be asked to review and
                        accept updated terms before continuing to use certain
                        services.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">14. Governing Law</h2>
                    <p>
                        These Terms and Conditions shall be governed by the
                        applicable laws of the Republic of the Philippines.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">15. Contact Information</h2>
                    <p>
                        For questions or concerns regarding these Terms and
                        Conditions, you may contact:
                    </p>
                    <p class="mt-3 font-semibold text-gray-800">
                        Padayon Massage Center
                    </p>
                    <p>Powered by DiverseCare Wellness Hub</p>
                    <p>
                        Address: RCPJC Building Unit 1, Brgy. Salcedo 2,
                        Noveleta, Philippines, 4105
                    </p>
                </section>

            </div>

            <div class="mt-10 border-t border-gray-200 pt-6">
                <a href="{{ route('privacy-policy') }}" class="text-sm font-semibold text-[#849753] hover:underline">
                    &larr; Read Privacy Policy
                </a>
            </div>

        </article>
    </div>
@endsection
