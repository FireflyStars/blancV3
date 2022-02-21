<template>

    <span class="tag" :style="style" :class="css_class" ><slot>{{status}}</slot></span>

</template>

<script>
    import {ref,watch} from 'vue';
    export default {
        name: "Tag",
        props:['name',"style"],
        setup(props){
            const css_class=ref('');
            const status=ref('');
            status.value=props.name.toLowerCase();
            css_class.value=props.name.replace(/ /g,'').toLowerCase();

            const statuses={
                inprogress:'In process', // if we want to show  In progress as in process
                //donatedtocharity:'Donated'
            };
            if(css_class.value in statuses){
                status.value=statuses[css_class.value];

            }
            watch(() => props.name, (current_val, previous_val) => {
                status.value=current_val.toLowerCase();
                css_class.value=current_val.replace(/ /g,'').toLowerCase();
                if(css_class.value in statuses){
                    status.value=statuses[css_class.value];

                }
            });
            return {
                css_class,
                status
            }
        }
    }
</script>

<style scoped>
.tag{
    font-family: "Gotham Rounded";
    text-transform: capitalize;
    background: #DDD;
    border-radius: 70px;
    text-align: center;
    font-size: 12px;
    font-weight: 400;
    width: 120px!important;
    height: 24px;
    position: relative;
    display: inline-block;
    vertical-align: middle;
    line-height: 24px;
}
    .tag.scheduled, .tag.checkinatelier, .tag.pickedup{
        background: #E0E0E0;
    }

    .tag.missedpickup,.tag.faileddelivery,.tag.late,.tag.latedelivery,.tag.overdueforcollection,.tag.overduestore,.tag.delete,.tag.void{
        color:rgba(235, 87, 87, 1);
        background: rgba(245, 171, 171, 0.7);
    }
    .tag.inprocess,.tag.partpending,.tag.partonhold{
        background: rgba(241, 210, 164, 0.7);
    }
    .tag.inprocess:before,.tag.partpending:before,.tag.partonhold:before,.tag.partiallydetailed:before{
        content: " ";
        background: #EF8F00;
        width: 12px;
        height: 6px;
        display: inline-block;
        border: 2px solid #EF8F00;
        position: absolute;
        left: 8px;
        top:12px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        border-top: 0;
    }
    .tag.inprocess:after,.tag.partpending:after,.tag.partonhold:after,.tag.partiallydetailed:after{
        content: " ";
        width: 12px;
        height: 6px;
        display: inline-block;
        border: 2px solid #EF8F00;
        position: absolute;
        left: 8px;
        top:6px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border-bottom: 0;
    }

.tag.fulfilled,.tag.ready,.tag.readyinstore{
    background: rgba(66, 167, 30, 0.2);
    color:#42A71E;
}
.tag.fulfilled:before,.tag.ready:before,.tag.readyinstore:before{
    content: " ";
    background: #42A71E;
    width: 12px;
    height:12px;
    display: inline-block;

    position: absolute;
    left: 8px;
    top:6px;
    border-radius: 8px;
}



.tag.assembling,.tag.offloaded{
    background: rgba(212, 221, 247, 0.7);
    color:#4E58E7;
}
.tag.assembling:before,.tag.offloaded:before{
    content: " ";
    background: #4E58E7;
    width: 12px;
    height: 6px;
    display: inline-block;
    border: 2px solid #4E58E7;
    position: absolute;
    left: 8px;
    top:12px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border-top: 0;
}
.tag.assembling:after,.tag.offloaded:after{
    content: " ";
    width: 12px;
    height: 6px;
    display: inline-block;
    border: 2px solid #4E58E7;
    position: absolute;
    left: 8px;
    top:6px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    border-bottom: 0;
}


.tag.onvan, .tag.deliveredtostore{
    background: rgba(234, 214, 247, 0.7);
    color: #9E44F2;
}
    .tag.unpaid{
        background:rgba(238, 238, 238, 1);
        color: #868686;
    }
.tag.paid{
    background:rgba(66, 167, 30, 0.2);
    color: #42A71E;
}
    .tag.instorage,.tag.donatedtocharity{
        background: #FFFFFF;
        border: 1px solid #000000;
    }

    .tag.b2c, .tag.b2b{
        width: auto!important;
        color: white;
        background-color: #47454B;
        text-transform: uppercase;
        padding: 0 10px;

    }
    .tag.b2b{
        background-color: #9E44F2;
    }
    .tag.vip{
        padding: 5px 8px;
        position: absolute;
        width: 64px !important;
        height: 24px;
        background: rgba(241, 210, 164, 0.7);
        border-radius: 4px;
        color: #B69968;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }
    .tag.partiallydetailed,.tag.tobedetailed{
        color: #EF8F00;
        background: rgba(241, 210, 164, 0.7);
        width: 160px!important;
    }
    .tag.detailed{
        background: #E0E0E0;
        color: #000000;
    }
    .tag.tobedetailed:before{
        content: " ";
        border: 2px solid #EF8F00;
        width: 12px;
        height:12px;
        display: inline-block;
        position: absolute;
        left: 8px;
        top:6px;
        border-radius: 8px;
    }
    .tag.detailed:before{
        content: " ";
        background: #000000;
        width: 12px;
        height:12px;
        display: inline-block;
        position: absolute;
        left: 8px;
        top:6px;
        border-radius: 8px;
        border: 2px solid #FFFFFF;

    }
</style>
