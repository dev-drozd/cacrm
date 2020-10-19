//////////////*Скрыть / показать текст*//////////////////
function readMore(){
    let dots = document.querySelector('.about-dots');
    let more = document.querySelector('.about-more');
    let btn = document.querySelector('.about-button');

    if(dots.style.display==='none'){
        dots.style.display='inline';
        btn.innerHTML="more";
        more.style.display='none';
    }
    else{
        dots.style.display='none';
        btn.innerHTML="hidden";
        more.style.display='inline';
    }
}

//////////////*Плавная прокрутка//////////////////
function smoothScroll(e,s) {
    e = document.getElementById(e || 'body');
    let x = 0,
		y = 0;
    while (e != null) {
        x += e.offsetLeft;
        y += e.offsetTop;
        e = e.offsetParent;
    }
    window.scrollTo(x, s ? y-s : y);
}



//////////////Выпадающий список//////////////////
function tamingselect() {
    if(!document.getElementById && !document.createTextNode){return;}
    var ts_selectclass='turnintodropdown';
    var ts_listclass='turnintoselect_demo2';
    var ts_boxclass='dropcontainer_demo2';
    var ts_triggeron='activetrigger_demo2';
    var ts_triggeroff='trigger_demo2';
    var ts_dropdownclosed='dropdownhidden_demo2';
    var ts_dropdownopen='dropdownvisible_demo2';
    var count=0;
    var toreplace=new Array();
    var sels=document.getElementsByTagName('select');
    for(var i=0;i<sels.length;i++){
        if (ts_check(sels[i],ts_selectclass))
        {
            var hiddenfield=document.createElement('input');
            hiddenfield.name=sels[i].name;
            hiddenfield.type='hidden';
            hiddenfield.id=sels[i].id;
            hiddenfield.value=sels[i].options[0].value;
            sels[i].parentNode.insertBefore(hiddenfield,sels[i])
            var trigger=document.createElement('a');
            ts_addclass(trigger,ts_triggeroff);
            trigger.href='#';
            trigger.onclick=function(){
                ts_swapclass(this,ts_triggeroff,ts_triggeron)
                ts_swapclass(this.parentNode.getElementsByTagName('ul')[0],ts_dropdownclosed,ts_dropdownopen);
                return false;
            }
            trigger.appendChild(document.createTextNode(sels[i].options[0].text));
            sels[i].parentNode.insertBefore(trigger,sels[i]);
            var replaceUL=document.createElement('ul');
            for(var j=0;j<sels[i].getElementsByTagName('option').length;j++)
            {
                var newli=document.createElement('li');
                var newa=document.createElement('a');
                newli.v=sels[i].getElementsByTagName('option')[j].value;
                newli.elm=hiddenfield;
                newli.istrigger=trigger;
                newa.href='#';
                newa.appendChild(document.createTextNode(
                    sels[i].getElementsByTagName('option')[j].text));
                    newli.onclick=function(){
                    this.elm.value=this.v;
                    ts_swapclass(this.istrigger,ts_triggeron,ts_triggeroff);
                    ts_swapclass(this.parentNode,ts_dropdownopen,ts_dropdownclosed)
                    this.istrigger.firstChild.nodeValue=this.firstChild.firstChild.nodeValue;
                    return false;
                }
                newli.appendChild(newa);
                replaceUL.appendChild(newli);
            }
            ts_addclass(replaceUL,ts_dropdownclosed);
            var div=document.createElement('div');
            div.appendChild(replaceUL);
            ts_addclass(div,ts_boxclass);
            sels[i].parentNode.insertBefore(div,sels[i])
            toreplace[count]=sels[i];
            count++;
        }
    }

    var uls=document.getElementsByTagName('ul');
    for(var i=0;i<uls.length;i++)
    {
        if(ts_check(uls[i],ts_listclass))
        {
            var newform=document.createElement('form');
            var newselect=document.createElement('select');
            for(j=0;j<uls[i].getElementsByTagName('a').length;j++)
            {
                var newopt=document.createElement('option');
                newopt.value=uls[i].getElementsByTagName('a')[j].href;
                newopt.appendChild(document.createTextNode(uls[i].getElementsByTagName('a')[j].innerHTML));
                newselect.appendChild(newopt);
            }
            newselect.onchange=function()
            {
                window.location=this.options[this.selectedIndex].value;
            }
            newform.appendChild(newselect);
            uls[i].parentNode.insertBefore(newform,uls[i]);
            toreplace[count]=uls[i];
            count++;
        }
    }
    for(i=0;i<count;i++){
        toreplace[i].parentNode.removeChild(toreplace[i]);
    }
    function ts_check(o,c)
    {
        return new RegExp('\\b'+c+'\\b').test(o.className);
    }
    function ts_swapclass(o,c1,c2)
    {
        var cn=o.className
        o.className=!ts_check(o,c1)?cn.replace(c2,c1):cn.replace(c1,c2);
    }
    function ts_addclass(o,c)
    {
        if(!ts_check(o,c)){o.className+=o.className==''?c:' '+c;}
    }
}

window.onload=function()
{
    tamingselect();
}


//////////////Мобильное меню//////////////////

document.querySelector('.header-button').onclick=function() {
    this.classList.toggle('active');
    document.querySelector('.header-nav').classList.toggle('active');
    document.body.classList.toggle('menu-mobile-open');
}


let item = document.querySelectorAll('.menu-dropdown');
for(let i in item){
    item[i].onclick=function(event) {
        event.preventDefault();
        this.classList.toggle('active');
    }
}

//////////////Скрытие / показ фильтров//////////////////
function showFilter() {
        document.querySelector('.category-block-left').classList.toggle('active');
        document.querySelector('.category-close').onclick=function () {
        document.querySelector('.category-block-left').classList.remove('active');
    }
}

let category = document.querySelectorAll('.category-block-left div');
for(let i in category){
    category[i].onclick=function() {
        this.classList.toggle('active');
    }
}

document.querySelector('.chat-icon').onclick=function () {
    document.body.classList.toggle('open-chat');
    document.querySelector('.chat-bottom').style.display="block";
    document.querySelector('.chat-dialog').style.display="none";

}

document.querySelector('.chat-close').onclick=function () {
    document.body.classList.toggle('open-chat');
}

document.querySelector('.chat-form button').onclick=function () {
    document.querySelector('.chat-bottom').style.display="none";
    document.querySelector('.chat-dialog').style.display="block";
}

//////////////Кнопка прокрутки вверх//////////////////

var goTopBtn = document.querySelector('.sckroll');
window.addEventListener('scroll', trackScroll);


function trackScroll() {
    var scrolled = window.pageYOffset;
    var coords = document.documentElement.clientHeight;

    if (scrolled > coords) {
        goTopBtn.classList.add('show');
    }
    if (scrolled < coords) {
        goTopBtn.classList.remove('show');
    }
}

function backToTop() {
    if (window.pageYOffset > 0) {
        window.scrollBy(0, -1000);
        setTimeout(backToTop, 100);
    }
}