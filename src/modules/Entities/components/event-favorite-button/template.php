<button 
    @click="toggleFavorite" 
    :class="buttonClass"
    :disabled="loading || !isLoggedIn"
    :title="buttonTitle"
>
    <mc-icon :name="iconName"></mc-icon>
    <span v-if="showCount && !iconOnly" class="favorite-count">{{ favoritesCount }}</span>
    <span v-if="!iconOnly" class="favorite-text">
        {{ isFavorited ? text('favorited') : text('favorite') }}
    </span>
</button>
