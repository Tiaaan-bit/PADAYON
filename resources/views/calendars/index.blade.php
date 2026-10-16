@extends($layout)

@section('title', 'Appointment Calendar')

@section('content')
    <div class="min-h-screen space-y-6 p-4 sm:p-6">

        {{-- PAGE HEADER --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Appointment Calendar
            </h1>
        </div>

        {{-- CALENDAR --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div id="calendar"></div>
        </div>


        {{-- APPOINTMENTS MODAL --}}
        <div id="appointments-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
            role="dialog" aria-modal="true" aria-labelledby="appointments-modal-title">
            <div id="appointments-modal-panel"
                class="flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
                {{-- Modal header --}}
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 sm:p-6">
                    <div>
                        <h2 id="appointments-modal-title" class="text-lg font-bold text-gray-900">
                            Appointments
                        </h2>

                        <p id="appointments-modal-count" class="mt-1 text-sm text-gray-500"></p>
                    </div>

                    <button type="button" id="close-appointments-modal"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-2xl text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                        aria-label="Close appointments modal">
                        &times;
                    </button>
                </div>

                {{-- Modal appointment list --}}
                <div id="appointments-modal-list" class="space-y-3 overflow-y-auto p-5 sm:p-6"></div>

                {{-- Modal footer --}}
                <div class="flex justify-end border-t border-gray-100 p-4">
                    <button type="button" id="appointments-modal-done"
                        class="rounded-lg bg-[#849753] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6f4e37]">
                        Close
                    </button>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('styles')

    <style>
        #calendar {
            width: 100%;
            min-height: 400px;
        }

        /* Hide individual appointments inside the calendar cells.
                       Appointment data is still used for counts and details. */
        #calendar .fc-daygrid-event,
        #calendar .fc-timegrid-event,
        #calendar .fc-daygrid-more-link,
        #calendar .fc-list-event {
            display: none !important;
        }

        /* Calendar appearance */
        #calendar .fc {
            font-family: inherit;
            color: #1f2937;
        }

        #calendar .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        #calendar .fc-button-primary {
            background-color: #849753;
            border-color: #849753;
            box-shadow: none;
        }

        #calendar .fc-button-primary:hover,
        #calendar .fc-button-primary:focus {
            background-color: #6f4e37;
            border-color: #6f4e37;
            box-shadow: none;
        }

        #calendar .fc-button-primary:disabled {
            background-color: #a7b58a;
            border-color: #a7b58a;
        }

        #calendar .fc-day-today {
            background-color: #f5f7ef !important;
        }

        #calendar .fc-col-header-cell-cushion,
        #calendar .fc-daygrid-day-number {
            color: #374151;
            text-decoration: none;
        }

        /* Center appointment count inside each calendar date box */
        .appointment-count-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: calc(100% - 12px);
            min-height: 30px;
            margin: 8px 6px 4px;
            padding: 5px 4px;
            box-sizing: border-box;
            border-radius: 8px;
            background: #849753;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
            text-align: center;
            overflow-wrap: anywhere;
        }

        /* Highlight today's date number with a circle */
        #calendar .fc-day-today .fc-daygrid-day-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin: 4px;
            padding: 0;
            border-radius: 50%;
            background: #849753;
            color: #fff !important;
            font-weight: 700;
            text-decoration: none;
        }

        /* Keep today's calendar cell subtly highlighted */
        #calendar .fc-day-today {
            background-color: #f5f7ef !important;
        }

        /* Responsive count badge */
        @media (max-width: 640px) {
            .appointment-count-badge {
                width: calc(100% - 6px);
                min-height: 26px;
                margin: 6px 3px 3px;
                padding: 4px 2px;
                font-size: 10px;
            }

            #calendar .fc-day-today .fc-daygrid-day-number {
                width: 28px;
                height: 28px;
            }
        }


        /* Indicates that a date can be selected */
        #calendar .fc-daygrid-day,
        #calendar .fc-col-header-cell {
            cursor: pointer;
        }

        #calendar .fc-daygrid-day:hover {
            background-color: #f8faf5;
        }

        /* Floating appointment tooltip */
        .appointment-hover-tooltip {
            position: fixed;
            z-index: 99999;
            width: min(360px, calc(100vw - 24px));
            max-height: min(420px, calc(100vh - 24px));
            overflow-y: auto;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 12px 35px rgb(0 0 0 / 15%);
            color: #374151;
            pointer-events: none;
        }

        .appointment-hover-tooltip .tooltip-title {
            margin-bottom: 10px;
            padding-bottom: 9px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .appointment-hover-tooltip .tooltip-appointment {
            padding: 9px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .appointment-hover-tooltip .tooltip-appointment:last-child {
            border-bottom: none;
        }

        .appointment-hover-tooltip .tooltip-customer {
            color: #111827;
            font-size: 13px;
            font-weight: 600;
        }

        .appointment-hover-tooltip .tooltip-detail {
            margin-top: 3px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        /* Appointment cards in the persistent list */
        .selected-appointment-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #fff;
        }

        .selected-appointment-card+.selected-appointment-card {
            margin-top: 12px;
        }

        .selected-appointment-time {
            color: #6f4e37;
            font-size: 13px;
            font-weight: 700;
        }

        .selected-appointment-customer {
            margin-top: 4px;
            color: #111827;
            font-size: 15px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .selected-appointment-details {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 16px;
        }

        .selected-appointment-detail-label {
            display: block;
            color: #9ca3af;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .selected-appointment-detail-value {
            display: block;
            margin-top: 2px;
            color: #4b5563;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        @media (max-width: 640px) {
            #calendar .fc-toolbar {
                gap: 10px;
                flex-direction: column;
                align-items: stretch;
            }

            #calendar .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 5px;
            }

            #calendar .fc-toolbar-title {
                font-size: 1.1rem;
            }

            #calendar .fc-button {
                padding: 0.35em 0.55em;
                font-size: 0.8rem;
            }

            .appointment-count-badge {
                margin: 4px 2px;
                padding: 3px 4px;
                font-size: 10px;
                white-space: normal;
            }

            .selected-appointment-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarElement = document.getElementById('calendar');

            if (!calendarElement) {
                return;
            }

            const eventsUrl = @json($eventsUrl);

            // Stores all appointments grouped by their appointment date.
            const appointmentsByDate = new Map();

            let calendar = null;

            /*
            |--------------------------------------------------------------------------
            | Date and time helpers
            |--------------------------------------------------------------------------
            */

            function getDateKey(value) {
                if (!value) {
                    return '';
                }

                if (typeof value === 'string') {
                    return value.substring(0, 10);
                }

                if (value instanceof Date) {
                    const year = value.getFullYear();
                    const month = String(value.getMonth() + 1).padStart(2, '0');
                    const day = String(value.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                }

                return '';
            }

            function formatDate(key) {
                if (!key) {
                    return 'Unknown date';
                }

                const parts = key.split('-');

                if (parts.length !== 3) {
                    return key;
                }

                // Construct a local date to avoid timezone shifting the date.
                const date = new Date(
                    Number(parts[0]),
                    Number(parts[1]) - 1,
                    Number(parts[2])
                );

                return date.toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function formatTime(value) {
                if (!value) {
                    return 'Time not specified';
                }

                // Expected event start format: YYYY-MM-DDTHH:mm:ss
                const match = String(value).match(/T(\d{2}):(\d{2})/);

                if (!match) {
                    return String(value);
                }

                const hours = Number(match[1]);
                const minutes = Number(match[2]);

                const date = new Date(2000, 0, 1, hours, minutes);

                return date.toLocaleTimeString('en-PH', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Group appointments by date
            |--------------------------------------------------------------------------
            */

            function groupAppointmentsByDate(events) {
                appointmentsByDate.clear();

                events.forEach(function(event) {
                    const key = getDateKey(event.start);

                    if (!key) {
                        return;
                    }

                    if (!appointmentsByDate.has(key)) {
                        appointmentsByDate.set(key, []);
                    }

                    appointmentsByDate.get(key).push(event);
                });

                // Keep appointments in chronological order.
                appointmentsByDate.forEach(function(appointments) {
                    appointments.sort(function(a, b) {
                        return String(a.start || '').localeCompare(
                            String(b.start || '')
                        );
                    });
                });
            }

            function getAppointmentsForDate(key) {
                return appointmentsByDate.get(key) || [];
            }

            function capitalizeFirst(value) {
                const text = String(value || 'Not specified');

                return text.charAt(0).toUpperCase() + text.slice(1);
            }

            function formatPaymentMethod(value) {
                const method = String(value || '').trim().toLowerCase();

                if (method === 'gcash') {
                    return 'GCash';
                }

                if (method === 'branch') {
                    return 'Pay at the Counter';
                }

                return value || 'Not specified';
            }

            /*
            |--------------------------------------------------------------------------
            | Appointment modal
            |--------------------------------------------------------------------------
            */

            function openAppointmentsModal(key) {
                const modal = document.getElementById('appointments-modal');
                const title = document.getElementById('appointments-modal-title');
                const count = document.getElementById('appointments-modal-count');
                const list = document.getElementById('appointments-modal-list');

                if (!modal || !title || !count || !list) {
                    console.error(
                        'Appointments modal elements are missing. ' +
                        'Check the modal HTML in your Blade view.'
                    );

                    return;
                }

                const appointments = getAppointmentsForDate(key);

                title.textContent = 'Appointments for ' + formatDate(key);

                count.textContent =
                    appointments.length + ' appointment' +
                    (appointments.length === 1 ? '' : 's');

                list.replaceChildren();

                if (appointments.length === 0) {
                    const empty = document.createElement('div');

                    empty.className =
                        'rounded-xl border border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500';

                    empty.textContent =
                        'No appointments scheduled for this date.';

                    list.appendChild(empty);
                }

                appointments.forEach(function(appointment) {
                    const props = appointment.extendedProps || {};

                    const card = document.createElement('article');

                    card.className =
                        'rounded-xl border border-gray-200 p-4 transition hover:border-[#849753]';

                    // Appointment time
                    const time = document.createElement('p');

                    time.className =
                        'text-sm font-semibold text-[#6f4e37]';


                    const startTime = formatTime(appointment.start);

                    const endTime = appointment.extendedProps?.appointment_end_time ?
                        formatTime(appointment.extendedProps.appointment_end_time) :
                        appointment.end ?
                        formatTime(appointment.end) :
                        null;

                    time.textContent = endTime ?
                        `${startTime} – ${endTime}` :
                        startTime;

                    // Customer name
                    const customer = document.createElement('h3');

                    customer.className =
                        'mt-1 text-base font-bold text-gray-900';

                    customer.textContent =
                        props.customer || 'Customer not specified';

                    // Appointment details
                    const details = document.createElement('div');

                    details.className =
                        'mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2';


                    const fields = [{
                            label: 'Service',
                            value: props.service || 'Not specified'
                        },
                        {
                            label: 'Description',
                            value: props.service_description || 'No description available'
                        },
                        {
                            label: 'Duration',
                            value: props.service_duration_minutes ?
                                `${props.service_duration_minutes} minutes` :
                                'Not specified'
                        },
                        {
                            label: 'Therapist',
                            value: props.therapist || 'Not assigned'
                        },
                        {
                            label: 'Appointment Status',
                            value: props.status || 'Not specified'
                        },
                        {
                            label: 'Payment Method',
                            value: formatPaymentMethod(props.payment_method)
                        },
                        {
                            label: 'Payment Status',
                            value: capitalizeFirst(props.payment_status || 'Not specified')
                        }
                    ];


                    fields.forEach(function(field) {
                        const wrapper = document.createElement('div');

                        const label = document.createElement('p');

                        label.className =
                            'text-xs font-medium uppercase tracking-wide text-gray-400';

                        label.textContent = field.label;

                        const value = document.createElement('p');

                        value.className =
                            'mt-1 break-words text-sm text-gray-700';

                        value.textContent = field.value;

                        wrapper.append(label, value);
                        details.appendChild(wrapper);
                    });

                    card.append(time, customer, details);
                    list.appendChild(card);
                });

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.body.style.overflow = 'hidden';

                const closeButton = document.getElementById(
                    'close-appointments-modal'
                );

                if (closeButton) {
                    closeButton.focus();
                }
            }

            function closeAppointmentsModal() {
                const modal = document.getElementById('appointments-modal');

                if (!modal) {
                    return;
                }

                modal.classList.add('hidden');
                modal.classList.remove('flex');

                document.body.style.overflow = '';
            }

            // Close buttons
            document
                .getElementById('close-appointments-modal')
                ?.addEventListener('click', closeAppointmentsModal);

            document
                .getElementById('appointments-modal-done')
                ?.addEventListener('click', closeAppointmentsModal);

            // Close by clicking the backdrop, not the modal content.
            document
                .getElementById('appointments-modal')
                ?.addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeAppointmentsModal();
                    }
                });

            // Close using the Escape key.
            document.addEventListener('keydown', function(event) {
                const modal = document.getElementById('appointments-modal');

                if (
                    event.key === 'Escape' &&
                    modal &&
                    !modal.classList.contains('hidden')
                ) {
                    closeAppointmentsModal();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Appointment count badges
            |--------------------------------------------------------------------------
            */


            function updateAppointmentCounts() {
                const dayCells = calendarElement.querySelectorAll(
                    '.fc-daygrid-day[data-date]'
                );

                dayCells.forEach(function(cell) {
                    const key = cell.getAttribute('data-date');
                    const appointments = getAppointmentsForDate(key);

                    const frame = cell.querySelector('.fc-daygrid-day-frame');

                    if (!frame) {
                        return;
                    }

                    let badge = frame.querySelector('.appointment-count-badge');

                    // Remove the badge if there are no appointments on this date.
                    if (appointments.length === 0) {
                        if (badge) {
                            badge.remove();
                        }

                        return;
                    }

                    // Create the badge if it does not exist.
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'appointment-count-badge';

                        const dayNumber = frame.querySelector(
                            '.fc-daygrid-day-top'
                        );

                        if (dayNumber) {
                            dayNumber.insertAdjacentElement('afterend', badge);
                        } else {
                            frame.prepend(badge);
                        }
                    }

                    // Display the total number of appointments.
                    badge.textContent =
                        appointments.length + ' appointment' +
                        (appointments.length === 1 ? '' : 's');

                    badge.title =
                        appointments.length + ' appointment' +
                        (appointments.length === 1 ? '' : 's') +
                        ' on ' + formatDate(key);
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Click a Month-view date to open its appointment modal
            |--------------------------------------------------------------------------
            */

            function attachDateClick(cell) {
                if (cell.dataset.appointmentClickAttached === 'true') {
                    return;
                }

                cell.dataset.appointmentClickAttached = 'true';

                cell.addEventListener('click', function(event) {
                    const key = cell.getAttribute('data-date');

                    if (!key) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    openAppointmentsModal(key);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Calendar initialization
            |--------------------------------------------------------------------------
            */

            calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',

                timeZone: 'Asia/Manila',

                height: 'auto',

                nowIndicator: true,

                navLinks: false,

                dayMaxEvents: false,

                // Individual events are hidden; date badges show totals.
                eventDisplay: 'none',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },

                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },

                /*
                 * Fetch appointments from the Laravel calendar endpoint.
                 * The endpoint should return a JSON array of FullCalendar events.
                 */
                events: function(fetchInfo, successCallback, failureCallback) {
                    const url = new URL(
                        eventsUrl,
                        window.location.origin
                    );

                    url.searchParams.set('start', fetchInfo.startStr);
                    url.searchParams.set('end', fetchInfo.endStr);

                    fetch(url.toString(), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error(
                                    'Unable to load appointments. HTTP ' +
                                    response.status
                                );
                            }

                            return response.json();
                        })
                        .then(function(events) {
                            if (!Array.isArray(events)) {
                                throw new Error(
                                    'The calendar endpoint must return an array of events.'
                                );
                            }

                            groupAppointmentsByDate(events);

                            successCallback(events);

                            requestAnimationFrame(function() {
                                updateAppointmentCounts();
                            });
                        })
                        .catch(function(error) {
                            console.error(
                                'Appointment calendar loading error:',
                                error
                            );

                            failureCallback(error);
                        });
                },

                /*
                 * Attach click behavior to each Month-view date cell.
                 */
                dayCellDidMount: function(info) {
                    attachDateClick(info.el);

                    requestAnimationFrame(function() {
                        updateAppointmentCounts();
                    });
                },

                /*
                 * In Week and Day views, clicking a date heading opens
                 * the appointment modal for that date.
                 */
                dayHeaderDidMount: function(info) {
                    const header = info.el;

                    if (header.dataset.appointmentHeaderClickAttached === 'true') {
                        return;
                    }

                    header.dataset.appointmentHeaderClickAttached = 'true';

                    header.style.cursor = 'pointer';

                    header.addEventListener('click', function(event) {
                        const key =
                            header.getAttribute('data-date') ||
                            getDateKey(info.date);

                        if (!key) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();

                        openAppointmentsModal(key);
                    });
                },

                /*
                 * Refresh appointment counts when FullCalendar updates events.
                 */
                eventsSet: function() {
                    requestAnimationFrame(function() {
                        updateAppointmentCounts();
                    });
                },

                /*
                 * Refresh counts and click handlers when changing views or dates.
                 */
                datesSet: function() {
                    requestAnimationFrame(function() {
                        calendarElement
                            .querySelectorAll('.fc-daygrid-day[data-date]')
                            .forEach(attachDateClick);

                        updateAppointmentCounts();
                    });
                }
            });

            calendar.render();
        });
    </script>
@endpush
