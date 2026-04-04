import registerAppointmentFormComponent from './appointmentForm';
import initLandingChatbot from './chatbot';
import initLandingNavigation from './navigation';

export default function initLandingPage() {
    registerAppointmentFormComponent();
    initLandingChatbot();
    initLandingNavigation();
}
