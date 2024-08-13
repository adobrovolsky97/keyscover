<template>
    <li>
        <details v-if="category.children && category.children.length" :open="isCategoryOpen(category)">
            <summary>
                <input type="checkbox" :checked="selectedCategories.includes(category.slug)"
                       @click="categoryClicked(category)" class="checkbox checkbox-sm"/>{{ category.name }}
            </summary>
            <ul>
                <category-item
                    v-on:category-clicked="categoryClicked"
                    :selected-categories="selectedCategories"
                    v-for="childCategory in category.children"
                    :key="childCategory.id"
                    :category="childCategory"
                ></category-item>
            </ul>
        </details>
        <span @click="categoryClicked(category)" v-else>
            <input type="checkbox" :checked="selectedCategories.includes(category.slug)" class="checkbox checkbox-sm"/>{{ category.name }}
        </span>
    </li>
</template>

<script>
export default {
    name: 'CategoryItem',
    props: {
        category: {
            type: Object,
            required: true
        },
        selectedCategories: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        categoryClicked(category) {
            this.$emit('category-clicked', category);
        },
        isCategoryOpen(category) {
            // Recursively check if the category or any of its descendants are selected
            if (this.selectedCategories.includes(category.slug)) {
                return true;
            }

            if (category.children) {
                return category.children.some(child => this.isCategoryOpen(child));
            }

            return false;
        }
    },
    watch: {
        selectedCategories() {
            // Re-evaluate the open state of the category when the selected categories change
            this.$forceUpdate();
        }
    }
};
</script>
