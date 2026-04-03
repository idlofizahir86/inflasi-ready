import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useTranslation() {
    const page = usePage();

    const locale = computed(() => page.props.locale || 'id');
    const translations = computed(() => page.props.translations || {});

    /**
     * Translate a key, with optional placeholder replacements.
     * Usage: t('Hello :name', { name: 'World' }) => 'Halo World'
     */
    function t(key, replacements = {}) {
        let text = translations.value[key] || key;
        Object.entries(replacements).forEach(([k, v]) => {
            text = text.replace(`:${k}`, v);
        });
        return text;
    }

    return { t, locale };
}
