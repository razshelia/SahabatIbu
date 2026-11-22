const io=new IntersectionObserver(es=>es.forEach(e=>e.target.classList.toggle('in',e.isIntersecting)),{rootMargin:'-40% 0px -40% 0px'});
document.querySelectorAll('.sec').forEach(e=>io.observe(e));
let SECS=[],SC_C={},V={};
function measure(){SECS=ORD.map(id=>{const e=$(id),r=e.getBoundingClientRect();return{id,k:e.dataset.k,c:r.top+scrollY+r.height/2}});SECS.forEach(s=>SC_C[s.id]=s.c);CH=Math.max(220,...[...document.querySelectorAll('#tools .card')].map(c=>c.offsetHeight||0))}
function calcViews(){['luar','home',...Object.keys(SP)].forEach(k=>V[k]=view(k))}
addEventListener('scroll',()=>{$('pg').style.transform='scaleX('+scrollY/Math.max(1,document.documentElement.scrollHeight-innerHeight)+')';$('cue').classList.toggle('off',scrollY>80)},{passive:true});
let drag=null;
const ray=new THREE.Raycaster(),vis=o=>{for(;o;o=o.parent)if(!o.visible)return false;return true};
// cari apa yang ada di bawah jari/kursor: tombol di layar alat lebih diutamakan daripada benda lain
function pick(cx,cy){ray.setFromCamera(new THREE.Vector2(cx/innerWidth*2-1,-(cy/innerHeight)*2+1),cam);
 const hs=ray.intersectObjects(hit,true).filter(x=>!x.object.isSprite&&vis(x.object));
 for(const h of hs){const o=h.object;if(o.hot&&h.uv){const px=h.uv.x*o.W,py=(1-h.uv.y)*o.H,b=o.hot.find(b=>px>=b.x-6&&px<=b.x+b.w+6&&py>=b.y-6&&py<=b.y+b.h+6);if(b)return{o,b,k:o.userData.k}}}
 const f=hs[0];return f?{o:f.object,k:f.object.userData.k}:null}
R.domElement.addEventListener('pointerdown',e=>{drag={x:e.clientX,y:e.clientY,m:false}});
addEventListener('pointermove',e=>{if(drag&&Math.abs(e.clientX-drag.x)+Math.abs(e.clientY-drag.y)>14)drag.m=true;
 if(e.pointerType!='mouse'||e.target!==R.domElement)return;const now=performance.now();if(now-(addEventListener.t||0)<60)return;addEventListener.t=now;
 const h=pick(e.clientX,e.clientY);R.domElement.style.cursor=h&&(h.b||h.o.userData.j||(h.k&&h.k!=cur))?'pointer':'default'});
addEventListener('pointerup',e=>{if(drag&&!drag.m&&e.target===R.domElement){const h=pick(e.clientX,e.clientY);
 if(h){const o=h.o,k=h.k;if(o.userData.j){const j=o.userData.j;S.sel=j;if(cur=='rak'){S.cart.push({j,kg:S.kg});S.m.rak=j+' '+fmt(S.kg)+' kg masuk keranjang';S.m.tm='';toast(j+' '+fmt(S.kg)+' kg masuk keranjang ('+S.cart.length+' baris). Lanjut ke Setor Sampah.')}redraw();popPile(j)}
  if(h.b&&cur==k)h.b.cb();
  else if(k=='luar')goSec('scan');else if(k&&k!=cur)goSec(k)}}drag=null});
function size(){if(!innerWidth||!innerHeight)return;R.setSize(innerWidth,innerHeight);cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix();measure();calcViews();nav()}
addEventListener('resize',size);addEventListener('load',()=>setTimeout(size,200));
const C={p:null,t:null},FOC={};let lastIdx=-1,lastStep='',frame=0,pT=performance.now();
function loop(){requestAnimationFrame(loop);const T0=performance.now(),t=T0/1000,dt=Math.min(.25,(T0-pT)/1000);pT=T0;const kk=1-Math.exp(-dt*7);frame++;if(frame%90==0)measure();
 const y=scrollY+innerHeight*.5;let i=0;while(i<SECS.length-2&&y>SECS[i+1].c)i++;
 const A=SECS[i],Bq=SECS[i+1],tt=cl((y-A.c)/(Bq.c-A.c)),s=sm(cl((tt-.28)/.44));
 const va=V[A.k],vb=V[Bq.k],G={p:[0,1,2].map(n=>lerp(va.p[n],vb.p[n],s)),t:[0,1,2].map(n=>lerp(va.t[n],vb.t[n],s))};
 if(G.p.some(isNaN)||G.t.some(isNaN)){size();return}
 if(!C.p||C.p.some(isNaN)||C.t.some(isNaN)){C.p=[...G.p];C.t=[...G.t]}
 for(let n=0;n<3;n++){C.p[n]+=(G.p[n]-C.p[n])*kk;C.t[n]+=(G.t[n]-C.t[n])*kk}
 cam.position.set(C.p[0]+Math.sin(T0/1900)*.06,C.p[1]+Math.cos(T0/2300)*.03,C.p[2]);cam.lookAt(C.t[0],C.t[1],C.t[2]);
 // pintu mengikuti scroll
 const dp=sm(cl(scrollY/(innerHeight*.28)));dr.scale.y=1-.92*dp;dl.intensity=dp*1.3;const dO=dp>.5;if(dO!==S.door){S.door=dO;redraw();nav()}
 // bagian aktif
 const idx=tt<.5?i:i+1;if(idx!==lastIdx){lastIdx=idx;curSec=SECS[idx].id;cur=SECS[idx].k;nav()}
 const ss=SECS[idx],p0=(y-ss.c)/(innerHeight*.6),si=Math.abs(p0)<.5?Math.floor((p0+.5)*3):-1,key=ss.id+si;
 if(key!==lastStep){lastStep=key;document.querySelectorAll('.st li.act').forEach(e=>e.classList.remove('act'));if(si>=0){const li=document.querySelectorAll('#'+ss.id+' .st li');if(li[si])li[si].classList.add('act')}}
 // fokus tiap alat
 Object.keys(GR).forEach(k=>{const f=sm(cl(1-Math.abs(y-SC_C[k])/(innerHeight*.75)));FOC[k]=f;const g=GR[k];g.rotation.y=SD[k]*(1-f)*.5;g.scale.setScalar(1+.05*f)});
 const pOf=k=>cl((y-SC_C[k])/(innerHeight*.6),-1,1);
 HD.forEach(h=>{h.visible=cur==h.k&&!S.busy&&(!h.cond||h.cond());const q=1+.08*Math.sin(t*6);h.scale.set(h.sx*q,h.sy*q,1);h.position.y=h.by+Math.sin(t*5)*.06});
 LB.forEach(l=>{const f=FOC[l.k]||0,q=1+.15*f;l.sp.scale.set(l.w*q,l.w/4*q,1);l.sp.material.opacity=MQ.matches?f:.55+.45*f;l.sp.visible=!MQ.matches||f>.35});
 RG.forEach(r=>{const f=FOC[r.k]||0;r.m.material.opacity=.12+.7*f*(.75+.25*Math.sin(t*4));r.m.scale.setScalar(1+.05*f*Math.sin(t*4))});
 if(boost>0)boost--;beam.position.z=boost?.3+Math.sin(t*14)*.3:.3+pOf('scan')*.3+Math.sin(t*1.2)*.05;
 bR.parent.rotation.y=-.04-pOf('riwayat')*.2+Math.sin(t*1.8)*.02;
 if(!S.busy){claw.position.x=Math.sin(pOf('capit')*2)*.9;claw.position.z=Math.cos(pOf('capit')*1.7)*.2;claw.rotation.z=Math.sin(t*1.4)*.05}
 rk.visible=!(cam.position.z>rk.position.z-2.5&&(cur=='capit'||cur=='riwayat'||cur=='struk'));sw.visible=cur=='luar';
 R.render(sc,cam)}
rcptDraw(null);size();redraw();nav();loop();
const SK='<span class="skel" aria-hidden="true"><i class="sk" style="width:38%;height:14px"></i><i class="sk" style="width:82%;height:34px"></i><i class="sk" style="width:100%;height:14px"></i><i class="sk" style="width:90%;height:14px"></i><i class="sk" style="width:100%;height:56px;border-radius:16px"></i><i class="sk" style="width:55%;height:46px;border-radius:999px"></i></span>';
document.querySelectorAll('.card,.grid>div').forEach(e=>e.insertAdjacentHTML('afterbegin',SK));
const ready=()=>setTimeout(()=>{document.documentElement.classList.remove('ld');size()},1100);
document.readyState=='complete'?ready():addEventListener('load',ready);
(function(){const o=openD;openD=function(i){o(i);const d=$('dlg'),h=d.innerHTML;d.innerHTML='<button class="x" onclick="$(\'dlg\').close()" aria-label="Tutup">×</button><i class="sk" style="width:64px;height:64px;border-radius:20px;margin-bottom:12px"></i><i class="sk" style="width:60%;height:30px;margin:10px 0"></i><i class="sk" style="width:100%;height:14px;margin:10px 0"></i><i class="sk" style="width:85%;height:14px"></i><i class="sk" style="width:100%;height:56px;margin-top:18px;border-radius:16px"></i><i class="sk" style="width:100%;height:56px;margin-top:9px;border-radius:16px"></i>';setTimeout(()=>{d.innerHTML=h},550)}})();
document.addEventListener('pointerdown',e=>{const b=e.target.closest('.btn,.tl,#keys button,.fold,.burger,.x');if(!b)return;const r=b.getBoundingClientRect(),d=Math.max(r.width,r.height)*2.2,p=document.createElement('span');p.className='rp';p.style.cssText='width:'+d+'px;height:'+d+'px;left:'+(e.clientX-r.left-d/2)+'px;top:'+(e.clientY-r.top-d/2)+'px';b.appendChild(p);setTimeout(()=>p.remove(),650)});
document.addEventListener('click',e=>{const c=e.target.closest('.card,.grid>div,.how>div,.st li');if(!c)return;c.classList.remove('pulse');void c.offsetWidth;c.classList.add('pulse')});
document.addEventListener('animationend',e=>e.target.classList.remove('pulse'));
document.addEventListener('touchstart',()=>{},{passive:true});
