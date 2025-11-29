app.component('agent-favorite-events', {
    template: $TEMPLATES['agent-favorite-events'],
    emits: [],

    setup() {
        const text = Utils.getTexts('agent-favorite-events')
        return { text }
    },

    data() {
        return {
            favorites: [],
            loading: true,
            error: null
        }
    },

    props: {
        agent: {
            type: Entity,
            required: true
        }
    },

    mounted() {
        this.loadFavorites();
    },

    computed: {
        hasFavorites() {
            return this.favorites.length > 0;
        }
    },

    methods: {
        async loadFavorites() {
            this.loading = true;
            this.error = null;

            try {
                const url = Utils.createUrl('event', 'favorites');
                const api = new API();
                const response = await api.GET(url);

                this.favorites = response.events || [];
            } catch (error) {
                console.error('Erro ao carregar favoritos:', error);
                this.error = this.text('loadError');
            } finally {
                this.loading = false;
            }
        },

        onUnfavorited(event) {
            // Remove o evento da lista quando desfavoritado
            this.favorites = this.favorites.filter(fav => fav.event.id !== event.id);
        }
    }
});
