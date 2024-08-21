<template>
    <li>
        <span @click="categoryClicked(category)" class="flex flex-row justify-start items-center">
            <input type="checkbox" :checked="selectedCategories.includes(category.slug)"
                   class="checkbox checkbox-sm"/><span class="text-sm">{{
                category.name
            }} ({{ category.products_count }})</span>
        </span>
        <div class="absolute -right-1 top-0" v-if="category.children.length > 0"
             @click="isOpen = !isOpen">
            <svg v-if="!isOpen" class="h-5 w-5 cursor-pointer" width="24" height="24" viewBox="0 0 24 24"
                 stroke-width="2"
                 stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path
                    stroke="none" d="M0 0h24v24H0z"/>
                <polyline points="6 9 12 15 18 9"/>
            </svg>

            <svg class="h-5 w-5 cursor-pointer" v-else width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                 stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path
                    stroke="none" d="M0 0h24v24H0z"/>
                <polyline points="6 15 12 9 18 15"/>
            </svg>
        </div>
        <ul class="menu-dropdown" :class="{'menu-dropdown-show': isOpen}">
            <category-item
                v-on:category-clicked="categoryClicked"
                :selected-categories="selectedCategories"
                v-for="childCategory in category.children"
                :key="childCategory.id"
                :category="childCategory"
            ></category-item>
        </ul>
        <!--        <details ref="root" v-if="category.children && category.children.length" :open="isOpen">-->
        <!--            <summary>-->
        <!--                <input type="checkbox" :checked="selectedCategories.includes(category.slug)"-->
        <!--                       @click="categoryClicked(category)" class="checkbox checkbox-sm"/>{{ category.name }}-->
        <!--            </summary>-->
        <!--            <ul>-->
        <!--                <category-item-->
        <!--                    v-on:category-clicked="categoryClicked"-->
        <!--                    :selected-categories="selectedCategories"-->
        <!--                    v-for="childCategory in category.children"-->
        <!--                    :key="childCategory.id"-->
        <!--                    :category="childCategory"-->
        <!--                ></category-item>-->
        <!--            </ul>-->
        <!--        </details>-->
        <!--        <span @click="categoryClicked(category)" v-else>-->
        <!--            <input type="checkbox" :checked="selectedCategories.includes(category.slug)" class="checkbox checkbox-sm"/>{{ category.name }}-->
        <!--        </span>-->
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
    data() {
        return {
            isOpen: false
        };
    },
    mounted() {
        this.isOpen = this.isCategoryOpen(this.category)
    },
    methods: {
        categoryClicked(category) {
            this.$emit('category-clicked', category);
        },
        isCategoryOpen(category) {
            this.isOpen = false;

            // Recursively check if the category or any of its descendants are selected
            if (this.selectedCategories.includes(category.slug)) {
                this.isOpen = true;
                return true;
            }

            if (category.children) {
                let res = category.children.some(child => this.isCategoryOpen(child));
                this.isOpen = res;
                return res;
            }

            return false;
        }
    },
    watch: {
        // selectedCategories() {
        //     // Re-evaluate the open state of the category when the selected categories change
        //     this.isCategoryOpen(this.category)
        //     console.log(this.isOpen)
        // }
    }
};
</script>
