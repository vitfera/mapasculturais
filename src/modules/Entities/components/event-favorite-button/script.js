app.component('event-favorite-button', {
    template: $TEMPLATES['event-favorite-button'],
    emits: ['favorited', 'unfavorited'],

    setup() {
        const text = Utils.getTexts('event-favorite-button')
        return { text }
    },

    data() {
        return {
            isFavorited: false,
            favoritesCount: 0,
            loading: false
        }
    },

    props: {
        event: {
            type: Entity,
            required: true
        },
        showCount: {
            type: Boolean,
            default: true
        },
        iconOnly: {
            type: Boolean,
            default: false
        }
    },

    mounted() {
        this.isFavorited = this.event.isFavorited || false;
        this.favoritesCount = this.event.favoritesCount || 0;
    },

    computed: {
        isLoggedIn() {
            return !!this.global.auth.user;
        },
        
        buttonClass() {
            return {
                'event-favorite-button': true,
                'is-favorited': this.isFavorited,
                'loading': this.loading,
                'icon-only': this.iconOnly
            };
        },

        iconName() {
            return this.isFavorited ? 'heart-fill' : 'heart';
        },

        buttonTitle() {
            if (!this.isLoggedIn) {
                return this.text('loginRequired');
            }
            return this.isFavorited ? this.text('removeFavorite') : this.text('addFavorite');
        }
    },

    methods: {
        async toggleFavorite() {
            if (!this.isLoggedIn) {
                const messages = useMessages();
                messages.error(this.text('loginRequired'));
                return;
            }

            if (this.loading) {
                return;
            }

            this.loading = true;

            try {
                const url = Utils.createUrl('event', 'toggleFavorite');
                const api = new API();
                const data = await api.POST(url, { eventId: this.event.id });

                this.isFavorited = data.favorited;
                this.favoritesCount = data.count;
                
                const messages = useMessages();
                if (this.isFavorited) {
                    messages.success(this.text('favoriteAdded'));
                    this.$emit('favorited', this.event);
                } else {
                    messages.success(this.text('favoriteRemoved'));
                    this.$emit('unfavorited', this.event);
                }
            } catch (error) {
                console.error('Erro ao favoritar evento:', error);
                const messages = useMessages();
                messages.error(this.text('error'));
            } finally {
                this.loading = false;
            }
        }
    }
});
