<template>
    <div
        class="departement"
        v-for="(dept, index) in departements"
        @click="chooseDepartement(dept.id)"
        :class="{ selected: itemDept == dept.id }"
    >
        <div class="departement-name">{{ dept.name }}</div>
    </div>
</template>
<script>
export default {
    name: "DetailingItemDepartement",
    components: {},
    props: {
        departements: {},
        itemDept: Number
    },
    emits: ['update-departement'],
    setup(props, context) {

        function chooseDepartement(id) {
            context.emit("update-departement", id);
        }

        return {
            chooseDepartement
        };
    },
}
</script>
<style scoped>
.departement {
    width: 253px;
    height: 235px;
    padding: 15px;
    margin: 15px;
    display: flex;
    background: #ffffff;
    box-shadow: 0px 0px 4px rgba(80, 80, 80, 0.2);
    border-radius: 4px;
    place-content: center;
}
.departement:hover {
    background-color: #47454b;
    color: #ffffff;
}
.departement.selected {
    background-color: #47454b;
    color: #ffffff;
}
.departement-name {
    font-family: Gotham Rounded;
    font-style: normal;
    font-weight: 400;
    font-size: 18px;
    line-height: 22px;
    display: flex;
    align-items: center;
    text-align: center;
}
</style>
