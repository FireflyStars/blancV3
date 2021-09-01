export default (to,from,next)=>{
    let htmltag=document.getElementsByTagName( 'html' )[0]

    htmltag.className='';
    htmltag.classList.add(to.name);
    if(to.name=="Login"&&sessionStorage.getItem('auth')){
        return next('/');
    }
    if(to.meta.authenticated&&sessionStorage.getItem('auth')){
       return next();
    }

    if(!to.meta.authenticated)
       return next();

    return next('/auth/login/');
};