@extends('layouts.home')

@section('title', 'Padayon Massage Center - Contacts')

@section('content')

    <div class="max-w-5xl w-full mx-auto p-10 text-gray-800 pt-20 pb-32">

        {{-- Header --}}
        <span class="px-2 py-1 text-xs border border-gray-300 rounded-full">
            Reach Out To Us
        </span>

        <h1 class="text-4xl font-bold text-left mt-4 bg-[#849753] bg-clip-text text-transparent">

            We'd love to Hear From You.

        </h1>

        <p class="text-left mt-4">

            We would love to hear from you and assist you with your needs.
            Whether you have questions, feedback, suggestions, or would like
            to book an appointment, our team is always ready to help.

            Feel free to contact us anytime, and we will do our best to provide
            you with a relaxing and satisfying experience.

        </p>


        {{-- Contact Information --}}
        <div class="flex flex-col md:flex-row gap-8 mt-16">


            {{-- Facebook --}}
            <div class="flex-1">

                <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#849753">

                    <path
                        d="M240-399.33h315.33V-466H240v66.67ZM240-526h480v-66.67H240V-526Zm0-126.67h480v-66.66H240v66.66ZM80-80v-733.33q0-27 19.83-46.84Q119.67-880 146.67-880h666.66q27 0 46.84 19.83Q880-840.33 880-813.33v506.66q0 27-19.83 46.84Q840.33-240 813.33-240H240L80-80Zm131.33-226.67h602v-506.66H146.67v575l64.66-68.34Zm-64.66 0v-506.66 506.66Z" />

                </svg>

                <p class="text-lg font-bold mt-2">
                    Facebook Page
                </p>

                <p class="text-gray-500 mt-1 mb-4">
                    Our team can respond in real time.
                </p>

                <a href="https://www.facebook.com/people/Padayon-Massage-Center/61583836714033/" target="_blank"
                    rel="noopener noreferrer" class="text-[#849753] font-semibold">

                    Padayon Massage Center

                </a>

            </div>


            {{-- Location --}}
            <div class="flex-1">

                <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px"
                    fill="#849753">

                    <path
                        d="M120-120v-556.67h163.33V-840h393.34v326.67H840V-120H528.67v-163.33h-97.34V-120H120Zm66.67-66.67h96.66v-96.66h-96.66v96.66Zm0-163.33h96.66v-96.67h-96.66V-350Zm0-163.33h96.66V-610h-96.66v96.67ZM350-350h96.67v-96.67H350V-350Zm0-163.33h96.67V-610H350v96.67Zm0-163.34h96.67v-96.66H350v96.66ZM513.33-350H610v-96.67h-96.67V-350Zm0-163.33H610V-610h-96.67v96.67Zm0-163.34H610v-96.66h-96.67v96.66Zm163.34 490h96.66v-96.66h-96.66v96.66Zm0-163.33h96.66v-96.67h-96.66V-350Z" />

                </svg>

                <p class="text-lg font-bold mt-2">
                    Visit Our Place
                </p>

                <p class="text-gray-500 mt-1 mb-4">
                    Visit our location in real life.
                </p>

                <span class="text-[#849753] font-semibold">

                    RCPJC Building Unit 1 Brgy. Salcedo 2,
                    Noveleta, Philippines, 4105

                </span>

            </div>


            {{-- Phone --}}
            <div class="flex-1">

                <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px"
                    fill="#849753">

                    <path
                        d="M796-120q-119 0-240-55.5T333-333Q231-435 175.5-556T120-796q0-18.86 12.57-31.43T164-840h147.33q14 0 24.34 9.83Q346-820.33 349.33-806l26.62 130.43q2.05 14.9-.62 26.24-2.66 11.33-10.82 19.48L265.67-530q24 41.67 52.5 78.5T381-381.33q35 35.66 73.67 65.5Q493.33-286 536-262.67l94.67-96.66q9.66-10.34 23.26-14.5 13.61-4.17 26.74-2.17L806-349.33q14.67 4 24.33 15.53Q840-322.27 840-308v144q0 18.86-12.57 31.43T796-120ZM233-592l76-76.67-21-104.66H187q3 41.66 13.67 86Q211.33-643 233-592Zm365.33 361.33q40.34 18.34 85.84 29.67 45.5 11.33 89.16 13.67V-288l-100-20.33-75 77.66ZM233-592Zm365.33 361.33Z" />

                </svg>

                <p class="text-lg font-bold mt-2">
                    Call Us Directly
                </p>

                <p class="text-gray-500 mt-1 mb-4">
                    Available during working hours.
                </p>

                <span class="text-[#849753] font-semibold">

                    0976 007 7035

                </span>

            </div>


            {{-- Working Hours --}}
            <div class="flex-1">

                <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px"
                    fill="#849753">

                    <path
                        d="m622-288.67 48.67-48.66-155.34-156v-195.34h-66.66v222l173.33 178ZM480-80q-82.33 0-155.33-31.5-73-31.5-127.34-85.83Q143-251.67 111.5-324.67T80-480q0-82.33 31.5-155.33 31.5-73 85.83-127.34Q251.67-817 324.67-848.5T480-880q82.33 0 155.33 31.5 73 31.5 127.34 85.83Q817-708.33 848.5-635.33T880-480q0 82.33-31.5 155.33-31.5 73-85.83 127.34Q708.33-143 635.33-111.5T480-80Zm0-400Zm0 333.33q137.67 0 235.5-97.83 97.83-97.83 97.83-235.5 0-137.67-97.83-235.5-97.83-97.83-235.5-97.83-137.67 0-235.5 97.83-97.83 97.83-97.83 235.5 0 137.67 97.83 235.5 97.83 97.83 235.5 97.83Z" />

                </svg>

                <p class="text-lg font-bold mt-2">
                    Working Hours
                </p>

                <p class="text-gray-500 mt-1 mb-4">
                    We’re here for you every day of the week!
                </p>

                <span class="text-[#849753] font-semibold">

                    Open Daily: 1:00 PM – 1:00 AM

                </span>

            </div>

        </div>

    </div>

@endsection
