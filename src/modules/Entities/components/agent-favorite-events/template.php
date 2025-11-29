<div class="agent-favorite-events">
    <div v-if="loading" class="loading">
        <p>{{ text('loading') }}</p>
    </div>

    <div v-else-if="error" class="error">
        <p>{{ error }}</p>
    </div>

    <div v-else-if="!hasFavorites" class="empty-state">
        <p>{{ text('noFavorites') }}</p>
    </div>

    <div v-else class="favorites-grid">
        <div v-for="favorite in favorites" :key="favorite.id" class="favorite-item">
            <entity-card :entity="favorite.event" type="event">
                <template #after-header>
                    <event-favorite-button 
                        :event="favorite.event" 
                        :show-count="false"
                        :icon-only="true"
                        @unfavorited="onUnfavorited"
                    />
                </template>
            </entity-card>
        </div>
    </div>
</div>
