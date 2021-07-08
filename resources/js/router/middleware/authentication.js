export default (to,from,next)=>{
    if(to.name=="Login"&&sessionStorage.getItem('auth')){
        return next('/');
    }
    if(to.meta.authenticated&&sessionStorage.getItem('auth')){
        next();
    }

    if(!to.meta.authenticated)
         next();

    return next('/auth/login/');
};