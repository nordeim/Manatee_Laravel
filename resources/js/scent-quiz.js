// resources/js/scent-quiz.js
// This will be an Alpine.js component data function
export default () => ({
    currentStep: 1,
    totalSteps: 3, // As per sample HTML
    answers: {},
    quizResult: null, // To store fetched/generated recommendations
    isLoadingResult: false,
    productRecommendations: [], // From sample HTML logic

    // Product data from sample_landing_page.html for demo recommendations
    allProducts: [
        { id: 1, name: "Serenity Blend Oil", desc: "Lavender & Chamomile", img: "scent2.jpg", tags: ["relax", "sleep", "floral", "herbal", "oil"] },
        { id: 2, name: "Focus Flow Oil", desc: "Rosemary & Mint", img: "scent4.jpg", tags: ["focus", "energize", "herbal", "oil"] },
        { id: 3, name: "Citrus Burst Soap", desc: "Lemon & Orange", img: "soap4.jpg", tags: ["energize", "focus", "citrus", "soap"] },
        { id: 4, name: "Woodland Retreat Soap", desc: "Cedarwood & Pine", img: "soap6.jpg", tags: ["relax", "grounding", "woody", "soap"] },
        { id: 5, name: "Uplift Blend Oil", desc: "Bergamot & Grapefruit", img: "scent5.jpg", tags: ["energize", "focus", "citrus", "oil"] },
        { id: 6, name: "Calm Embrace Soap", desc: "Sandalwood & Vanilla", img: "soap1.jpg", tags: ["relax", "sleep", "woody", "sweet", "soap"] }
    ],

    nextStep(stepContext, value) {
        this.answers[stepContext] = value;
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
        } else {
            this.showResult();
        }
    },

    showResult() {
        this.isLoadingResult = true;
        document.getElementById('scentQuizForm').style.display = 'none'; // Hide form
        document.getElementById('quizResultDisplay').style.display = 'block'; // Show result area

        // Simulate fetching or processing results
        // In a real app, this might be an AJAX call to QuizController@submit
        // For now, use the logic from sample_landing_page.html
        const { feeling, scentFamily, format } = this.answers;
        let recommended = this.allProducts.filter(p => {
            let match = false;
            if (p.tags.includes(feeling)) match = true;
            if (p.tags.includes(scentFamily)) match = true;
            if (format !== 'both' && !p.tags.includes(format)) return false;
            return match;
        });

        if (recommended.length < 2) {
            const generalRecs = this.allProducts.filter(p => !recommended.includes(p) && (format === 'both' || p.tags.includes(format)));
            recommended = [...recommended, ...generalRecs.slice(0, 2 - recommended.length)];
        }
        if (recommended.length < 2) {
           recommended = this.allProducts.filter(p => format === 'both' || p.tags.includes(format)).slice(0, 2);
        }
        this.productRecommendations = recommended.slice(0, 2); // Max 2 results for quiz display

        this.isLoadingResult = false;

        // Example of sending data to backend (would replace above client-side logic)
        /*
        axios.post('{{ route('quiz.submit') }}', this.answers)
            .then(response => {
                this.productRecommendations = response.data.recommendations;
                this.isLoadingResult = false;
            })
            .catch(error => {
                console.error('Error submitting quiz:', error);
                // Display an error message to the user
                this.isLoadingResult = false;
            });
        */
    },

    get quizStepNumText() {
        return `${this.currentStep} of ${this.totalSteps}`;
    }
});
