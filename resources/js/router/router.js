import {
    createRouter,
    createWebHistory,
} from  'vue-router';
import NotFound from '../components/NotFound';
import AuthenticationMiddleware from './middleware/authentication';
import Assembly from '../components/assembly/Assembly';

const router = createRouter({
    history:createWebHistory(),
    routes:[
        {
            path:'',
            name:'LandingPage',
            component:()=> import('../components/orderlist/OrderList'),
            meta:{
                authenticated:true
            },
            children:[
                {
                    path:'/order_details/:order_id',
                    name:'OrderDetails',
                    component:()=> import('../components/orderlist/OrderDetail'),
                    meta:{
                        authenticated:true
                    }
                }
                ]
        },
        {
            path:'/order-content/:order_id',
            name:'DetailingItemList',
            component:()=> import('../components/detailing/DetailingItemList'),
            meta:{
                authenticated:true
            }
        },
        {
            path:'/detailing_item/:order_id/:item_id',
            name:'DetailingItem',
            component:()=> import('../components/detailing/DetailingItem'),
            meta:{
                authenticated:true
            }
        },
        {
            path:'/component-test',
            name:'ComponentTest',
            component:()=> import('../components/orderlist/ComponentTest'),
            meta:{
                authenticated:true
            },
        },
        {
            path:'/neworder',
            name:'NewOrder',
            component:()=> import('../components/neworder/NewOrder'),
            meta:{
                authenticated:true
            },
        },
        {
            path:'/permissions',
            name:'Permissions',
            component:()=> import('../components/permission/Permission'),
            meta:{
                authenticated:true,
                superadmin:true,
            },
        },
        {
            path:'/auth/',
            name:'AuthPage',
            component:()=> import('../components/auth/AuthPage'),
            meta:{
                authenticated:false
            },
            children:[
                {
                    path:'/auth/login/',
                    name:'Login',
                    component:()=> import('../components/auth/Login'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/forgot-password/',
                    name:'ForgotPassword',
                    component:()=> import('../components/auth/ForgotPassword'),
                    meta:{
                        authenticated:false
                    }
                },
                {
                    path:'/auth/new-password/:recovery_token',
                    name:'NewPassword',
                    component:()=> import('../components/auth/NewPassword'),
                    meta:{
                        authenticated:false
                    }
                }
            ]
        },
        {
            path:'/loader/',
            name:'Loader',
            component:()=> import('../components/WaveLoader'),
            meta:{
                authenticated:false
            }
        },
        {
            path:'/stats',
            name:'Statistics',
            component:()=> import('../components/statistics/Statistics'),
            meta:{
                authenticated:true
            },
        },
        {
            path: '/:pathMatch(.*)',
            name: 'not-found',
            component:NotFound
        },
        {
            path: '/assembly-home',
            name: 'Assembly',
            meta:{
                authenticated: true
            },
            component: Assembly,
        },
    ]
});

router.beforeEach(AuthenticationMiddleware);

export default router;
