export default function appointmentWizard(
    oldServiceId = '',
    oldTherapistId = '',
    oldLevel = '',
    oldAddOnId = '',
    oldPreviousOps = '',
    oldBodyProblem = '',
    oldDate = '',
    oldTime = '',
    oldPaymentMethod = '',
    oldPaymentType = '',
    services = [],
    therapists = [],
    addOns = [],
    availableSlotsUrl = ''
) {
    return {
        step: 1,

        serviceFilter: 'Bed Massage',

        service_id: oldServiceId || '',
        therapist_id: oldTherapistId || '',
        level: oldLevel || '',
        add_on_id: oldAddOnId || '',
        has_previous_operations: oldPreviousOps || '',
        body_problem: oldBodyProblem || '',
        appointment_date: oldDate || '',
        appointment_time: oldTime || '',
        payment_method: oldPaymentMethod || '',
        payment_type: oldPaymentType || '',

        services,
        therapists,
        addOns,

        availableSlots: [],
        loadingSlots: false,
        today: '',
        availableSlotsUrl,

        init() {
            const now = new Date();

            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');

            this.today = `${year}-${month}-${day}`;

            if (
                this.service_id &&
                this.therapist_id &&
                this.appointment_date
            ) {
                this.loadSlots();
            }
        },

        get selectedService() {
            return this.services.find(
                service =>
                    String(service.id) === String(this.service_id)
            ) || null;
        },

        get selectedServiceName() {
            return this.selectedService
                ? this.selectedService.name
                : '';
        },

        get selectedTherapist() {
            return this.therapists.find(
                therapist =>
                    String(therapist.id) === String(this.therapist_id)
            ) || null;
        },

        get selectedTherapistName() {
            return this.selectedTherapist
                ? this.selectedTherapist.name
                : '';
        },

        get selectedAddOn() {
            return this.addOns.find(
                addon =>
                    String(addon.id) === String(this.add_on_id)
            ) || null;
        },

        get selectedAddOnName() {
            return this.selectedAddOn
                ? this.selectedAddOn.name
                : 'None';
        },

        get totalAmount() {
            const servicePrice = this.selectedService
                ? Number(this.selectedService.price)
                : 0;

            const addOnPrice = this.selectedAddOn
                ? Number(this.selectedAddOn.price)
                : 0;

            return servicePrice + addOnPrice;
        },

        get totalDuration() {
            const serviceMinutes = this.selectedService
                ? Number(this.selectedService.duration_minutes)
                : 0;

            const addOnMinutes = this.selectedAddOn
                ? Number(this.selectedAddOn.duration_minutes)
                : 0;

            return serviceMinutes + addOnMinutes;
        },

        get selectedSlotLabel() {
            const slot = this.availableSlots.find(
                slot => slot.start === this.appointment_time
            );

            return slot ? slot.label : '';
        },

        canGoNext() {
            if (this.step === 1) {
                return Boolean(
                    this.service_id &&
                    this.level &&
                    this.has_previous_operations !== ''
                );
            }

            if (this.step === 2) {
                return Boolean(this.therapist_id);
            }

            if (this.step === 3) {
                return Boolean(
                    this.appointment_date &&
                    this.appointment_time
                );
            }

            if (this.step === 4) {
                if (this.payment_method === 'gcash') {
                    return Boolean(this.payment_type);
                }

                return Boolean(this.payment_method);
            }

            return false;
        },

        nextStep() {
            if (
                this.step < 4 &&
                this.canGoNext()
            ) {
                this.step++;
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
            }
        },

        async loadSlots() {
            this.availableSlots = [];
            this.appointment_time = '';

            if (
                !this.service_id ||
                !this.therapist_id ||
                !this.appointment_date
            ) {
                return;
            }

            this.loadingSlots = true;

            const url =
                `${this.availableSlotsUrl}` +
                `?therapist_id=${encodeURIComponent(this.therapist_id)}` +
                `&date=${encodeURIComponent(this.appointment_date)}` +
                `&service_id=${encodeURIComponent(this.service_id)}` +
                `&add_on_id=${encodeURIComponent(this.add_on_id || '')}`;

            try {
                const response = await fetch(url, {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(
                        `Failed to load slots: ${response.status}`
                    );
                }

                const data = await response.json();

                this.availableSlots = Array.isArray(data)
                    ? data
                    : [];

            } catch (error) {
                console.error(
                    'Unable to load appointment slots:',
                    error
                );

                this.availableSlots = [];

            } finally {
                this.loadingSlots = false;
            }
        }
    };
}