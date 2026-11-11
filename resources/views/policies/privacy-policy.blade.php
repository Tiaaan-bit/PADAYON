@extends('layouts.auth')

@section('title', 'Privacy Policy | Padayon Massage Center')

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
                    Privacy Policy
                </h1>

                <p class="mt-3 text-sm text-gray-500">
                    Last Updated: November 10, 2026
                </p>
            </div>

            <div class="space-y-7 text-sm leading-7 text-gray-600">

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">1. Introduction</h2>
                    <p>
                        Padayon Massage Center, powered by DiverseCare Wellness Hub,
                        respects your privacy and is committed to protecting your
                        personal information.
                    </p>
                    <p class="mt-2">
                        This Privacy Policy explains how we collect, use, store,
                        process, and protect your personal information when you
                        visit our website, create an account, book an appointment,
                        make payments, or use our services.
                    </p>
                    <p class="mt-2">
                        We handle personal information in accordance with Republic
                        Act No. 10173, also known as the Data Privacy Act of 2012,
                        and its applicable implementing rules and regulations.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">2. Information We Collect</h2>

                    <p>Depending on how you use our website and services, we may collect:</p>

                    <h3 class="mt-3 font-semibold text-gray-800">A. Personal Information</h3>
                    <ul class="ml-5 list-disc">
                        <li>Full name</li>
                        <li>Email address</li>
                        <li>Mobile number</li>
                        <li>Account information</li>
                    </ul>

                    <h3 class="mt-3 font-semibold text-gray-800">B. Appointment Information</h3>
                    <ul class="ml-5 list-disc">
                        <li>Selected massage services</li>
                        <li>Preferred therapist</li>
                        <li>Appointment date and time</li>
                        <li>Appointment history and status</li>
                        <li>Massage preferences and add-on services</li>
                    </ul>

                    <h3 class="mt-3 font-semibold text-gray-800">C. Health-Related Information</h3>
                    <p>
                        We may collect information voluntarily provided by customers
                        to help us understand their service requirements, including:
                    </p>
                    <ul class="ml-5 list-disc">
                        <li>Previous operations or surgeries</li>
                        <li>Body problems or physical discomfort</li>
                        <li>Relevant health-related concerns</li>
                    </ul>
                    <p class="mt-2">
                        Health-related information may qualify as sensitive personal
                        information under Philippine data privacy laws. We handle
                        this information with appropriate care and confidentiality.
                    </p>

                    <h3 class="mt-3 font-semibold text-gray-800">D. Payment Information</h3>
                    <ul class="ml-5 list-disc">
                        <li>Payment method</li>
                        <li>Transaction references</li>
                        <li>Payment amounts and status</li>
                        <li>Downpayment and remaining balance records</li>
                    </ul>
                    <p class="mt-2">
                        Payment providers may process transaction information.
                        We do not request your GCash password or PIN.
                    </p>

                    <h3 class="mt-3 font-semibold text-gray-800">E. Technical Information</h3>
                    <ul class="ml-5 list-disc">
                        <li>IP address</li>
                        <li>Browser and device information</li>
                        <li>Website session information</li>
                        <li>Security and activity records</li>
                    </ul>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">3. How We Use Your Information</h2>
                    <p>We may use your information for the following purposes:</p>
                    <ul class="ml-5 list-disc">
                        <li>Creating and managing customer accounts.</li>
                        <li>Processing appointment bookings.</li>
                        <li>Managing appointment schedules and therapist assignments.</li>
                        <li>Providing massage services.</li>
                        <li>Reviewing relevant health disclosures for service considerations.</li>
                        <li>Processing and verifying payments.</li>
                        <li>Sending appointment confirmations and reminders.</li>
                        <li>Responding to customer inquiries.</li>
                        <li>Maintaining website security.</li>
                        <li>Complying with applicable legal obligations.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">4. Sharing of Information</h2>
                    <p>
                        We do not sell customers' personal information. However,
                        information may be accessed or shared when necessary with:
                    </p>
                    <ul class="ml-5 list-disc">
                        <li>Authorized administrators and staff.</li>
                        <li>Assigned massage therapists who need relevant appointment information.</li>
                        <li>Payment providers, including PayMongo, when processing transactions.</li>
                        <li>SMS providers, including UniSMS, when delivering notifications.</li>
                        <li>Website hosting and technology providers.</li>
                        <li>Other parties when required or authorized by law.</li>
                    </ul>
                    <p class="mt-2">
                        We limit access and disclosure to information necessary for
                        the relevant purpose.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">5. Data Storage and Retention</h2>
                    <p>
                        We retain personal information only for as long as necessary
                        to fulfill the purposes for which it was collected, comply
                        with legal obligations, maintain business records, and
                        establish or defend legal claims.
                    </p>
                    <p class="mt-2">
                        When information is no longer necessary and there is no
                        lawful reason to retain it, we will take appropriate steps
                        to securely delete, destroy, or anonymize it.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">6. Data Protection</h2>
                    <p>
                        We implement reasonable organizational, physical, and
                        technical measures to protect personal information against
                        unauthorized access, disclosure, alteration, loss, and
                        misuse.
                    </p>
                    <p class="mt-2">
                        Access to customer information is limited to authorized
                        personnel who need it to perform their responsibilities.
                        However, no electronic system can be guaranteed to be
                        completely secure.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">7. Your Privacy Rights</h2>
                    <p>
                        Subject to applicable laws and limitations, you may exercise
                        the following rights:
                    </p>
                    <ul class="ml-5 list-disc">
                        <li>Right to be informed.</li>
                        <li>Right to access your personal information.</li>
                        <li>Right to request correction of inaccurate information.</li>
                        <li>Right to object to certain processing activities.</li>
                        <li>Right to request erasure or blocking when legally applicable.</li>
                        <li>Right to data portability when applicable.</li>
                        <li>Right to seek damages when legally entitled.</li>
                        <li>Right to file a complaint with the National Privacy Commission.</li>
                    </ul>
                    <p class="mt-2">
                        You may contact Padayon Massage Center to make a privacy
                        request or raise a concern.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">8. Cookies and Website Sessions</h2>
                    <p>
                        Our website may use cookies and session technologies to
                        support essential functions such as account authentication,
                        website navigation, security, and maintaining customer
                        sessions.
                    </p>
                    <p class="mt-2">
                        Additional analytics or tracking technologies, if
                        implemented, will be appropriately disclosed.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">9. Third-Party Services</h2>
                    <p>
                        Our website may use third-party services for payment
                        processing, SMS notifications, hosting, and other
                        operational requirements. These providers may process
                        information necessary to deliver their services.
                    </p>
                    <p class="mt-2">
                        Their processing is subject to applicable privacy
                        requirements and their respective privacy policies.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">10. Changes to This Policy</h2>
                    <p>
                        We may update this Privacy Policy when necessary due to
                        changes in our services, business operations, technology,
                        or applicable laws. Updated versions will be published
                        on this page with a revised date.
                    </p>
                </section>

                <section>
                    <h2 class="mb-2 text-lg font-bold text-gray-800">11. Contact Information</h2>
                    <p>
                        For privacy questions, requests, or concerns, you may
                        contact:
                    </p>
                    <p class="mt-3 font-semibold text-gray-800">
                        Padayon Massage Center
                    </p>
                    <p>Powered by DiverseCare Wellness Hub</p>
                    <p>
                        Address: RCPJC Building Unit 1, Brgy. Salcedo 2,
                        Noveleta, Philippines, 4105
                    </p>
                    <p>
                        National Privacy Commission:
                        <a href="https://privacy.gov.ph/" target="_blank" rel="noopener noreferrer"
                            class="font-medium text-[#849753] hover:underline">
                            https://privacy.gov.ph/
                        </a>
                    </p>
                </section>

            </div>

            <div class="mt-10 border-t border-gray-200 pt-6">
                <a href="{{ route('terms-and-conditions') }}" class="text-sm font-semibold text-[#849753] hover:underline">
                    Read Terms and Conditions &rarr;
                </a>
            </div>

        </article>
    </div>
@endsection
