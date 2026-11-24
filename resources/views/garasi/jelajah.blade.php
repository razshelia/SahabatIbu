<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Garasi Bank Sampah 3D</title>
<style>
:root{--ink:#eef5f0;--glass:#141c18a8;--line:#ffffff1f;--green:#6fe0a2;--yel:#f3c64f;box-sizing:border-box}
*{box-sizing:border-box;margin:0}
html,body{height:100%;overflow:hidden;background:#1b211e;color:var(--ink);font-family:"Inter","Segoe UI","Trebuchet MS",sans-serif}
body{padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
canvas{display:block;width:100%;height:100%;touch-action:none}
body:after{content:"";position:fixed;inset:0;pointer-events:none;background:radial-gradient(ellipse at 50% 45%,#0000 55%,#000a 100%)}
#top{position:fixed;z-index:5;left:16px;right:16px;top:calc(14px + env(safe-area-inset-top,0px));display:flex;align-items:center;justify-content:space-between;gap:12px;pointer-events:none}
#top>*{pointer-events:auto}
.g{background:var(--glass);border:1px solid var(--line);backdrop-filter:blur(18px) saturate(1.5);-webkit-backdrop-filter:blur(18px) saturate(1.5);box-shadow:0 10px 30px #0006,inset 0 1px 0 #ffffff1a}
.brand{display:flex;gap:10px;align-items:center;padding:8px 16px 8px 10px;border-radius:18px}
.brand i{width:30px;height:30px;border-radius:10px;background:conic-gradient(from 200deg,#6fe0a2,#2f7d5b,#f3c64f,#6fe0a2);box-shadow:0 0 18px #6fe0a255}
.brand b{font-size:14px;display:block;line-height:1.1}.brand span{font-size:11px;opacity:.6}
#nav{position:relative;display:flex;gap:2px;padding:5px;border-radius:999px}
#nav button{position:relative;z-index:1;background:none;border:0;color:var(--ink);font:inherit;font-size:12.5px;font-weight:650;padding:9px 14px;border-radius:999px;cursor:pointer;opacity:.72;white-space:nowrap;transition:color .2s,opacity .2s}
#nav button.on{color:#0f1f17;opacity:1}
#nav button:hover{opacity:1}
#nav button:focus-visible,.sw:focus-visible{outline:2px solid var(--green);outline-offset:2px}
#ind{position:absolute;top:5px;bottom:5px;left:0;border-radius:999px;background:linear-gradient(#f4fbf6,#bfe9d1);transition:transform .45s cubic-bezier(.3,1.3,.5,1),width .3s;box-shadow:0 4px 14px #6fe0a244}
.right{display:flex;gap:8px;align-items:center}
.sw{display:flex;align-items:center;gap:9px;border-radius:999px;padding:8px 14px 8px 9px;color:var(--ink);font:inherit;font-size:12.5px;font-weight:650;cursor:pointer}
.sw .k{width:34px;height:20px;border-radius:12px;background:#ffffff2a;position:relative;transition:background .3s}
.sw .k:after{content:"";position:absolute;left:3px;top:3px;width:14px;height:14px;border-radius:50%;background:#fff;transition:transform .3s}
.sw.on .k{background:var(--green)}.sw.on .k:after{transform:translateX(14px)}
.sw em{font-style:normal;opacity:.7;font-weight:500}
.bal{border-radius:18px;padding:7px 15px;text-align:right}.bal small{font-size:10px;opacity:.6;display:block}.bal b{font-size:15px;color:var(--green)}
#hint{pointer-events:none;position:fixed;z-index:5;left:50%;transform:translateX(-50%);top:calc(78px + env(safe-area-inset-top,0px));width:max-content;max-width:min(760px,92vw);padding:10px 18px;border-radius:999px;font-size:15px;font-weight:700;line-height:1.35;text-align:center;color:#fff;background:#0f1a15e6;border:1px solid #6fe0a266;box-shadow:0 8px 24px #0009;pointer-events:none;text-shadow:0 1px 2px #000a;transition:opacity .7s}
@media(max-width:1040px){#nav{position:fixed;left:50%;transform:translateX(-50%);bottom:calc(12px + env(safe-area-inset-bottom,0px));max-width:96vw;overflow-x:auto}.brand span,.sw em{display:none}}
@media(max-width:520px){.brand b{display:none}#hint{font-size:13px;padding:8px 14px;border-radius:18px}.bal{padding:5px 10px}.bal b{font-size:13px}.sw{padding:8px 10px;font-size:11.5px}.right{gap:6px}}
@keyframes bump{40%{transform:scale(1.14)}}.bump{animation:bump .45s}
.pop{position:fixed;z-index:9;font-weight:800;font-size:16px;pointer-events:none;animation:pop 1.4s ease-out forwards;text-shadow:0 2px 8px #000b}.pop.up{color:#6fe0a2}.pop.dn{color:#ff8a7a}
@keyframes pop{from{opacity:0;transform:translate(-50%,-6px)}15%{opacity:1}to{opacity:0;transform:translate(-50%,38px)}}
#note{position:fixed;z-index:9;left:50%;top:calc(132px + env(safe-area-inset-top,0px));transform:translate(-50%,-10px);opacity:0;max-width:92vw;padding:11px 20px;border-radius:999px;font-weight:800;font-size:15px;color:#0f1f17;background:#6fe0a2;box-shadow:0 10px 26px #0008;pointer-events:none;transition:.28s;text-align:center}#note.on{opacity:1;transform:translate(-50%,0)}

#remote{position:fixed;z-index:6;left:50%;transform:translateX(-50%);bottom:calc(10px + env(safe-area-inset-bottom,0px));width:min(940px,98vw);display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:22px;background:linear-gradient(165deg,#fffdf6f2,#e6dec9f2);color:#26332d;box-shadow:0 14px 30px #0007,inset 0 2px 0 #fff,inset 0 -4px 0 #0000001a;border:1px solid #fff9;transition:opacity .25s}
#remote.dim{opacity:.18;pointer-events:none}
.home{flex:none;width:44px;height:44px;border-radius:50%;border:0;cursor:pointer;background:radial-gradient(circle at 35% 30%,#a8f5c8,#3fae78);box-shadow:0 4px 0 #23744f,0 6px 10px #0004;display:grid;place-items:center;transition:transform .12s,box-shadow .12s}
.home:active,.home.on{transform:translateY(3px);box-shadow:0 1px 0 #23744f}
.lcd{flex:none;width:148px;background:linear-gradient(#12201a,#0b1511);border-radius:12px;padding:5px 10px;box-shadow:inset 0 0 0 2px #0008,inset 0 0 14px #6fe0a222;color:#8ef0be}
.lcd b{font-size:14px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.lcd span{font-size:10px;color:#f3c64f}
.ar{flex:none;display:flex;gap:4px}.ar button{width:32px;height:36px;border-radius:11px;border:0;background:#d4cbb4;color:#26332d;font-size:11px;cursor:pointer;box-shadow:0 3px 0 #a79d84}
.ar button:active{transform:translateY(2px);box-shadow:0 1px 0 #a79d84}
#keys{flex:1;display:grid;grid-template-columns:repeat(7,1fr);gap:5px}
#keys button{border:0;border-radius:11px;height:42px;font:inherit;font-weight:800;font-size:13px;color:#26332d;cursor:pointer;background:var(--c);box-shadow:0 3px 0 var(--d);transition:transform .1s,box-shadow .1s}
#keys button:hover{transform:translateY(-1px)}
#keys button.on{transform:translateY(3px);box-shadow:0 0 0 var(--d),inset 0 0 0 2px #2fbf71}
.slide{flex:none;display:flex;align-items:center;gap:7px;border:0;border-radius:999px;padding:6px 11px 6px 6px;font:inherit;font-size:11.5px;font-weight:800;color:#26332d;background:#d4cbb4;box-shadow:inset 0 2px 4px #0003;cursor:pointer}
.slide .k{width:32px;height:20px;border-radius:11px;background:#a79d84;position:relative;transition:background .3s}
.slide .k:after{content:"";position:absolute;left:3px;top:3px;width:14px;height:14px;border-radius:50%;background:#fff;box-shadow:0 2px 3px #0004;transition:transform .3s}
.slide.on .k{background:#2fbf71}.slide.on .k:after{transform:translateX(12px)}
.slide em{font-style:normal;font-weight:600;opacity:.65}
.fold{flex:none;width:28px;height:36px;border:0;border-radius:10px;background:#d4cbb4;color:#26332d;cursor:pointer;transition:transform .2s}
#pad{position:fixed;z-index:6;right:14px;bottom:calc(96px + env(safe-area-inset-bottom,0px));display:grid;grid-template-columns:repeat(3,46px);gap:6px;padding:8px;border-radius:20px}
#pad button{height:46px;border:0;border-radius:14px;background:#ffffff1c;color:var(--ink);font:inherit;font-size:12px;font-weight:800;cursor:pointer;touch-action:none;display:grid;place-items:center;gap:0;line-height:1.05}
#pad button:active,#pad button.h{background:var(--green);color:#0f1f17}
#pad button:focus-visible{outline:2px solid var(--green);outline-offset:2px}
#pad b{font-size:17px;display:block}
@media(max-width:900px){#pad{bottom:calc(150px + env(safe-area-inset-bottom,0px));right:10px;grid-template-columns:repeat(3,42px)}#pad button{height:42px}}
#pad{display:flex;grid-template-columns:none;align-items:center;gap:10px;padding:10px;border-radius:26px}
#pad .zm{display:flex;flex-direction:column;gap:8px}#pad .zm button{width:46px;height:46px}#pad .zm b{font-size:22px}
#joy{position:relative;width:124px;height:124px;border-radius:50%;background:radial-gradient(circle at 50% 45%,#ffffff26,#ffffff0d 70%);border:2px solid #ffffff33;box-shadow:inset 0 4px 14px #0006;touch-action:none;cursor:grab}
#joy .a{position:absolute;font-style:normal;font-size:12px;opacity:.55;color:var(--ink)}#joy .u{left:50%;top:7px;transform:translateX(-50%)}#joy .d{left:50%;bottom:7px;transform:translateX(-50%)}#joy .l{left:9px;top:50%;transform:translateY(-50%)}#joy .r{right:9px;top:50%;transform:translateY(-50%)}
#knob{position:absolute;left:50%;top:50%;width:54px;height:54px;margin:-27px 0 0 -27px;border-radius:50%;background:radial-gradient(circle at 35% 30%,#a8f5c8,#3fae78);box-shadow:0 5px 0 #23744f,0 8px 14px #0006;transition:transform .18s}
#joy.on{cursor:grabbing}#joy.on #knob{transition:none}
@media(max-width:900px){#pad{padding:8px;gap:8px}#joy{width:108px;height:108px}#knob{width:48px;height:48px;margin:-24px 0 0 -24px}}
#remote.min #keys{display:none}#remote.min .fold{transform:rotate(180deg)}#remote.min{width:auto}
#remote button:focus-visible{outline:3px solid #2f7d5b;outline-offset:2px}
@media(max-width:900px){#remote{flex-wrap:wrap;padding:6px 8px;gap:6px;border-radius:18px}.lcd{flex:1;width:auto}#keys{order:9;flex:0 0 100%}#keys button{height:34px;font-size:10px;border-radius:9px}.slide em{display:none}}
</style></head><body>
<header id="top"><div class="brand g"><img class="logo" src="{{ asset('assets/logo-mark.svg') }}" alt="" width="34" height="34"><div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div></div><div class="right"><div class="bal g" id="balbox"><small>Saldo</small><b id="balv"></b></div><div class="bal g" id="dombox"><small>Dompet tunai</small><b id="domv"></b></div>@auth
<a class="sw g" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('nasabah.dashboard') }}" style="text-decoration:none">&larr; Kembali</a>
@else
<a class="sw g" href="{{ url('/') }}" style="text-decoration:none">&larr; Beranda</a><a class="sw g" href="{{ route('login') }}" style="text-decoration:none">Masuk / Daftar</a>
@endauth</div></header>
<div id="remote" role="toolbar" aria-label="Remote garasi">
<button class="home" onclick="goStart()" aria-label="Kembali ke awal, di luar garasi"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d2a1b" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v10h13V10"/><path d="M10 20v-6h4v6"/></svg></button>
<div class="lcd" aria-live="polite"><b id="loc">Luar garasi</b><span id="ds2">Pintu tertutup</span></div>
<div class="ar"><button onclick="step(-1)" aria-label="Alat sebelumnya">◀</button><button onclick="step(1)" aria-label="Alat berikutnya">▶</button></div>
<div id="keys"></div>
<button id="doorBtn" class="slide" onclick="toggleDoor()" aria-label="Buka atau tutup pintu garasi"><span class="k"></span>Pintu <em id="ds">tutup</em></button>
<button class="fold" onclick="this.parentNode.classList.toggle('min')" aria-label="Ciutkan atau perluas tombol alat">▾</button></div>
<div id="pad" class="g" role="group" aria-label="Kendali jalan dan zoom"><div id="joy" role="application" aria-label="Joystick: geser ke segala arah untuk berjalan"><i class="a u">&#9650;</i><i class="a d">&#9660;</i><i class="a l">&#9664;</i><i class="a r">&#9654;</i><div id="knob"></div></div><div class="zm"><button data-h="zi" aria-label="Zoom masuk"><b>+</b></button><button data-h="zo" aria-label="Zoom keluar"><b>&minus;</b></button></div></div>
<div id="hint">Garasi masih tertutup. Ketuk pintu atau panel di sampingnya untuk membuka.</div><div id="note" role="status" aria-live="polite"></div>
@php
    $curUser = auth()->user();
    $nasabah = $curUser?->nasabah;
    $namaNasabah = $nasabah ? $curUser->name : ($curUser?->role === 'admin' ? 'Ibu Ani (Demo)' : ($curUser?->name ?? 'Ani'));
    $nomorNasabah = $nasabah?->nomor_nasabah ?? 'NSB-0842';
    $saldoAwal = ($nasabah && (float)$nasabah->saldo_tersedia >= 10000) 
        ? (float)$nasabah->saldo_tersedia 
        : 50000;
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="{{ asset('assets/js/dekor.js') }}"></script>
<script>
const USER_NAME = @js($namaNasabah);
const USER_NO = @js($nomorNasabah);
const rp=n=>'Rp '+Math.round(n).toLocaleString('id-ID');
const fmt=k=>k.toFixed(2).replace('.',',');
const J={Plastik:{h:3000,c:0x6fb4e6,d:'Botol PET bersih, tanpa tutup dan label.'},Kertas:{h:2000,c:0xd9b27a,d:'Kertas dan kardus kering, diikat per tumpuk.'},Logam:{h:5000,c:0xaab3bb,d:'Kaleng dan besi, sisa isi dibersihkan.'}};
const S={stok:{Plastik:2.5,Kertas:2,Logam:1},dompet:0,sync:0,bs:{{ $saldoAwal }},bt:0,saldo:{{ $saldoAwal }},tahan:0,riw:[],cart:[],tarik:[],n:1,tn:1,sel:'Plastik',kg:1,nom:{{ $saldoAwal >= 10000 ? 10000 : 0 }},busy:false,f:'all',m:{}};
const avail=()=>S.saldo-S.tahan;
const $=id=>document.getElementById(id);

const R=new THREE.WebGLRenderer({antialias:true});R.setPixelRatio(Math.min(devicePixelRatio,2));R.shadowMap.enabled=true;R.shadowMap.type=THREE.PCFSoftShadowMap;
document.body.prepend(R.domElement);
const sc=new THREE.Scene();sc.background=new THREE.Color(0x7d9bab);sc.fog=new THREE.Fog(0x7d9bab,30,70);
const cam=new THREE.PerspectiveCamera(42,1,.1,60);
sc.add(new THREE.HemisphereLight(0xfff3dc,0x4a4a40,.8));
const sun=new THREE.SpotLight(0xfff0d0,1.3,40,.9,.5);sun.position.set(2,11,6);sun.castShadow=true;sun.shadow.mapSize.set(1536,1536);sc.add(sun);
const M=(c,r=.6,m=0)=>new THREE.MeshStandardMaterial({color:c,roughness:r,metalness:m});
function box(w,h,d,mat,x,y,z,p){const m=new THREE.Mesh(new THREE.BoxGeometry(w,h,d),mat);m.position.set(x,y,z);m.castShadow=m.receiveShadow=true;(p||sc).add(m);return m}
function cyl(r,h,mat,x,y,z,p,s=24){const m=new THREE.Mesh(new THREE.CylinderGeometry(r,r,h,s),mat);m.position.set(x,y,z);m.castShadow=true;(p||sc).add(m);return m}
const rr=(g,x,y,w,h,r)=>{g.beginPath();g.roundRect?g.roundRect(x,y,w,h,r):g.rect(x,y,w,h)};
const tx=(g,s,x,y,sz=34,col='#8ef0be',a='left',wt='800')=>{g.fillStyle=col;g.font=wt+' '+sz+'px Trebuchet MS';g.textAlign=a;g.fillText(s,x,y)};
const bg=(g,W,H,c='#12201a')=>{const q=g.createLinearGradient(0,0,0,H);q.addColorStop(0,c);q.addColorStop(1,'#0b1511');g.fillStyle=q;g.fillRect(0,0,W,H)};
const SC=[];
function screen(w,h,x,y,z,p,ry=0,px=512){const c=document.createElement('canvas');c.width=px;c.height=Math.round(px*h/w);const g=c.getContext('2d');const t=new THREE.CanvasTexture(c);t.anisotropy=8;const m=new THREE.Mesh(new THREE.PlaneGeometry(w,h),new THREE.MeshBasicMaterial({map:t}));m.position.set(x,y,z);m.rotation.y=ry;(p||sc).add(m);m.hot=[];m.W=c.width;m.H=c.height;
 const B=(bx,by,bw,bh,l,cb,k='n')=>{const C={g:['#6fe0a2','#0d2a1b'],r:['#ff8a7a','#3a0f09'],y:['#f3c64f','#2b1f00'],n:['#2c3b34','#eef5f0'],on:['#eef5f0','#16201b'],p:['#e3d7bd','#3a2f1e'],pon:['#2f7d5b','#ffffff']}[k];g.globalAlpha=cb?1:.35;g.fillStyle=C[0];rr(g,bx,by,bw,bh,bh*.28);g.fill();g.fillStyle=C[1];g.font='800 '+Math.round(bh*.42)+'px Trebuchet MS';g.textAlign='center';g.fillText(l,bx+bw/2,by+bh*.64);g.globalAlpha=1;if(cb)m.hot.push({x:bx,y:by,w:bw,h:bh,cb})};
 m.paint=f=>{m.draw=f;m.render()};m.render=()=>{if(!m.draw)return;m.hot=[];m.draw(g,c.width,c.height,B);t.needsUpdate=true};SC.push(m);return m}
const hit=[];function tag(g,k){g.traverse(o=>{o.userData.k=k});hit.push(g)}
const redraw=()=>{SC.forEach(s=>s.render());chip('balv','balbox',avail());chip('domv','dombox',S.dompet)};
const last={};
function chip(id,box,val){const e=$(id);if(!e)return;e.textContent=rp(val);const o=last[id];if(o!==undefined&&Math.round(val)!==Math.round(o)){const b=$(box);b.classList.remove('bump');void b.offsetWidth;b.classList.add('bump');pop(box,(val>o?'+':'−')+rp(Math.abs(val-o)),val>o)}last[id]=val}
function pop(boxId,t,up){const b=$(boxId).getBoundingClientRect(),d=document.createElement('div');d.className='pop '+(up?'up':'dn');d.textContent=t;d.style.left=(b.left+b.width/2)+'px';d.style.top=(b.bottom+4)+'px';document.body.appendChild(d);setTimeout(()=>d.remove(),1500)}
function hintFade(on){clearTimeout(hintFade.t);const h=$('hint');h.style.opacity=1;if(on)hintFade.t=setTimeout(()=>h.style.opacity=0,5000)}
let noteT;function note(m){const n=$('note');n.textContent=m;n.classList.add('on');clearTimeout(noteT);noteT=setTimeout(()=>n.classList.remove('on'),2600)}

// ruangan
const HH=6.4;box(21,.3,19.5,M(0x6f7a72,.35,.1),0,-.15,4.75);box(90,.3,70,M(0x58656e,.9),0,-.2,50);
[3,8.5].forEach(z=>box(21,.02,.14,M(0xf3c64f,.5),0,.01,z));
const ceil=box(21,.3,19.5,M(0x8d928b,.9),0,HH+.15,4.75);ceil.castShadow=false;
box(21,HH,.3,M(0xcfc9b8,.9),0,HH/2,-4.5);
for(let i=-10;i<=10;i+=1.5)box(.04,HH,.04,M(0xb5af9e,1),i,HH/2,-4.32);
box(.3,HH,19.5,M(0xc3bdac,.9),-10.5,HH/2,4.75);box(.3,HH,19.5,M(0xc3bdac,.9),10.5,HH/2,4.75);
for(let i=-1;i<=1;i++){const l=box(3,.15,.6,M(0xffffff,.2),i*6,HH-.5,1);l.material.emissive=new THREE.Color(0xfff4d0);l.material.emissiveIntensity=.8}
const sign=screen(5,.85,0,HH-1,-4.3);sign.paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'BANK SAMPAH SAHABAT IBU',W/2,H*.5,31,'#fff','center');tx(g,'Perum. Taman Gading, Jember',W/2,H*.82,20,'#cfeedd','center','600')});

// 1 TITIK KUMPUL WARGA: gerobak berisi kantong sampah titipan warga. Ketuk kantong untuk dimasukkan ke stok Ibu.
const row=new THREE.Group();row.scale.setScalar(.72);row.position.set(0,0,-2.5);sc.add(row);
const cream=M(0xf1ead8,.5);
const pm=new THREE.Group();pm.position.set(-11.05,0,0);row.add(pm);
const cwood=M(0xa9774a,.7),cdark=M(0x8a5f38,.8);
box(3.2,.2,1.8,cwood,0,.75,0,pm);box(3.2,.5,.12,cdark,0,1.05,.84,pm);box(3.2,.5,.12,cdark,0,1.05,-.84,pm);box(.12,.5,1.8,cdark,-1.54,1.05,0,pm);box(.12,.5,1.8,cdark,1.54,1.05,0,pm);
[-1.1,1.1].forEach(x=>[-.95,.95].forEach(z=>{const w=cyl(.45,.14,M(0x26332d,.6),x,.45,z,pm,28);w.rotation.x=Math.PI/2;cyl(.12,.16,M(0xd4cbb4),x,.45,z,pm,12).rotation.x=Math.PI/2}));
box(.1,.1,1.5,cdark,-1.95,1.25,0,pm);[-.7,.7].forEach(z=>box(.5,.08,.08,cdark,-1.7,1.0,z,pm));
[-1.4,1.4].forEach(x=>box(.12,2.95,.12,M(0x26332d),x,2.33,-.8,pm));
box(3.2,.95,.1,M(0x26332d),0,3.35,-.86,pm);
const pmS=screen(3.0,.9,0,3.35,-.8,pm,0,768);
pmS.paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'TITIK KUMPUL WARGA',W/2,H*.4,50,'#fff','center','900');
 tx(g,'Stok Ibu: Plastik '+fmt(S.stok.Plastik)+' · Kertas '+fmt(S.stok.Kertas)+' · Logam '+fmt(S.stok.Logam)+' kg',W/2,H*.68,27,'#f3c64f','center','800');
 tx(g,S.m.scan||'Ketuk kantong untuk mengambil sampah',W/2,H*.9,24,'#cfeedd','center','700')});
tag(pm,'scan');
const NAMA=['Bu Sri','Bu Rina','Bu Wati','Bu Dewi','Pak RT','Bu Lastri','Bu Tini','Bu Yuli'],SLOT=[[-1,-.35],[0,-.35],[1,-.35],[-1,.35],[0,.35],[1,.35]],bags=[];
function mkBag(b){const g=new THREE.Group(),col=b.j=='Logam'?0x6f7c86:J[b.j].c,mt=M(col,.7);
 const body=new THREE.Mesh(new THREE.SphereGeometry(.34,20,16),mt);body.scale.set(1,1.1,.9);body.position.y=.38;body.castShadow=true;g.add(body);
 const neck=new THREE.Mesh(new THREE.ConeGeometry(.13,.28,12),mt);neck.position.y=.78;g.add(neck);
 const tie=new THREE.Mesh(new THREE.TorusGeometry(.1,.025,8,16),M(0xf3c64f,.5));tie.rotation.x=Math.PI/2;tie.position.y=.66;g.add(tie);
 b.scr=screen(.5,.32,0,.36,.31,g,0,256);b.scr.paint((gg,W,H)=>{gg.fillStyle='#fff8e6';gg.fillRect(0,0,W,H);tx(gg,b.who,W/2,H*.3,34,'#3a2f1e','center','900');tx(gg,b.j,W/2,H*.56,28,'#6b5a3d','center','700');tx(gg,fmt(b.kg)+' kg',W/2,H*.88,42,'#1f6b46','center','900')});
 g.position.set(SLOT[b.slot][0],1.12,SLOT[b.slot][1]);g.rotation.y=(b.slot%3-1)*.15;g.traverse(o=>{o.userData.k='scan';o.userData.bag=b});pm.add(g);b.g=g}
function spawnBag(){if(bags.length>=6)return;const used=bags.map(b=>b.slot),free=[0,1,2,3,4,5].filter(i=>!used.includes(i));if(!free.length)return;const r=Math.random(),j=r<.4?'Plastik':r<.8?'Kertas':'Logam';
 const b={j,kg:.5+Math.floor(Math.random()*11)*.25,who:NAMA[Math.floor(Math.random()*NAMA.length)],slot:free[Math.floor(Math.random()*free.length)]};bags.push(b);mkBag(b);b.g.scale.setScalar(.01);redraw();
 tw(p=>b.g.scale.setScalar(.01+.99*p+Math.sin(p*Math.PI)*.18),550)}
async function collectBag(b){if(b.busy)return;b.busy=true;bags.splice(bags.indexOf(b),1);S.stok[b.j]+=b.kg;S.m.scan='+'+fmt(b.kg)+' kg '+b.j+' dari '+b.who;note(b.who+' menitip '+fmt(b.kg)+' kg '+b.j+'. Masuk stok Ibu!');
 const g=b.g,x0=g.position.x,y0=g.position.y;redraw();
 await tw(p=>{g.position.x=x0+p*4.2;g.position.y=y0+Math.sin(p*Math.PI)*1.6;g.rotation.y=p*9;g.scale.setScalar(1-.9*p)},750);
 pm.remove(g);SC.splice(SC.indexOf(b.scr),1);redraw()}
let bagN=0,bagInit=false;
function bagTick(){if(!S.door)return;const t=performance.now();if(!bagInit){bagInit=true;[0,1,2].forEach(()=>spawnBag());bagN=t+9000}if(t>bagN){spawnBag();bagN=t+8000+Math.random()*6000}}

// 2 TIMBANGAN
const tm=new THREE.Group();tm.position.set(-.55,0,0);row.add(tm);
box(2.7,.35,2.2,cream,0,.18,0,tm);cyl(.9,.12,M(0xb4bcc4,.3,.8),0,.46,.1,tm,40);
box(2.2,2.3,.5,cream,0,1.45,-.8,tm);const tmS=screen(1.9,1.55,0,1.5,-.54,tm);
screen(2.5,.28,0,.2,1.12,tm).paint((g,W,H)=>{bg(g,W,H,'#f1ead8');tx(g,'TIMBANGAN CATAT SETORAN',W/2,H*.72,40,'#26332d','center')});
tag(tm,'timbang');let wShow=0;
tmS.paint((g,W,H,B)=>{bg(g,W,H);tx(g,'Transaksi baru',24,42,24,'#9fd9b8','left','600');tx(g,USER_NO+' / '+USER_NAME,W-24,42,24,'#fff','right');if(S.m.tm)tx(g,S.m.tm,24,76,19,'#f3c64f','left','600');tx(g,fmt(wShow)+' kg',24,146,62);
 {const L=S.cart,rh=34,vt=170,vb=H-120,vh=vb-vt,ov=L.length*rh>vh,mx=Math.max(0,L.length*rh-vh);S.tmMax=mx;S.tmOff=Math.max(0,Math.min(mx,S.tmOff||0));const xr=ov?W-100:W-24;
  if(L.length)tx(g,L.length+' baris',W-24,146,22,'#9fd9b8','right','600');
  if(!L.length){tx(g,'Keranjang kosong.',24,206,24,'#9fd9b8','left','700');tx(g,'Pilih sampah di rak katalog.',24,238,22,'#9fd9b8','left','600')}
  else{g.save();g.beginPath();g.rect(0,vt,W,vh);g.clip();L.forEach((r,i)=>{const y=vt+26+i*rh-S.tmOff;if(y<vt-rh||y>vb+rh)return;tx(g,r.j+' '+fmt(r.kg)+' kg',24,y,24,'#eef5f0','left','600');tx(g,rp(r.kg*J[r.j].h),xr,y,24,'#eef5f0','right','600')});g.restore();
   if(ov){B(W-88,vt,60,56,'▲',S.tmOff>0?()=>{S.tmOff-=rh*2;redraw()}:null,'n');B(W-88,vb-56,60,56,'▼',S.tmOff<mx?()=>{S.tmOff+=rh*2;redraw()}:null,'n');
    const th=Math.max(24,vh*vh/(L.length*rh));g.fillStyle='#ffffff22';g.fillRect(W-16,vt,8,vh);g.fillStyle='#9fd9b8';g.fillRect(W-16,vt+(vh-th)*(S.tmOff/mx),8,th)}}}
 tx(g,'Total '+rp(S.cart.reduce((a,r)=>a+r.kg*J[r.j].h,0)),24,H-96,34,'#f3c64f');
 B(24,H-70,270,54,'Catat & timbang',S.busy||!S.cart.length?null:doSetor,'g');B(308,H-70,180,54,'Kosongkan',S.cart.length&&!S.busy?()=>{S.cart=[];redraw()}:null,'n')});

// 3 PRINTER
const pr=new THREE.Group();pr.position.set(2.05,0,0);row.add(pr);
box(1.7,.9,1.3,cream,0,.45,0,pr);box(1.7,.5,.9,cream,0,1.12,-.2,pr);box(1.3,.08,.1,M(0x26332d),0,1.0,.62,pr);
const prS=screen(1.5,.5,0,.4,.66,pr);
const paper=screen(1.1,1.5,0,.9,.2,pr);paper.scale.y=.01;paper.rotation.x=-.25;
tag(pr,'struk');let lastR=null,paperOut=false;
prS.paint((g,W,H,B)=>{bg(g,W,H);tx(g,lastR?lastR.k+' · '+rp(lastR.v):'Belum ada struk',20,52,30,'#fff');B(20,H-86,230,62,'Cetak ulang',lastR&&!S.busy?()=>printRcpt(lastR):null,'y');tx(g,paperOut?'Ketuk struk: sobek!':lastR?'Saldo '+rp(lastR.s):'Catat setoran dulu',W-20,H-44,24,paperOut?'#f3c64f':'#9fd9b8','right','800')});
function rcptDraw(r){paper.paint((g,W,H)=>{g.fillStyle='#fff';g.fillRect(0,0,W,H);const L=r?[['BANK SAMPAH SAHABAT IBU','c'],[r.k+' · '+r.t,'c'],['-'],...r.rows,['-'],['TOTAL',rp(r.v)],['Saldo '+rp(r.s)],['Terima kasih, '+USER_NAME,'c']]:[['Belum ada struk','c']];L.forEach((l,i)=>{const y=60+i*44;if(l[0]=='-'){g.fillStyle='#999';g.fillRect(20,y-14,W-40,2)}else if(l[1]=='c')tx(g,l[0],W/2,y,26,'#222','center');else{tx(g,l[0],26,y,25,'#222','left','600');if(l[1])tx(g,l[1],W-26,y,25,'#222','right')}})})}

// 4 BUKU RIWAYAT (meja baca di kiri, terbuka dari depan)
const bk=new THREE.Group();bk.position.set(5,0,0);row.add(bk);
box(3.4,.1,2.3,M(0xa9774a,.7),0,1.0,0,bk);[[-1.5,-1],[1.5,-1],[-1.5,1],[1.5,1]].forEach(p=>box(.12,1,.12,M(0x8a5f38),p[0],.5,p[1],bk));
box(3.1,.3,.5,M(0x5a3d28),0,1.2,-.7,bk);
const bb=new THREE.Group();bb.position.set(0,1.38,.1);bb.rotation.x=-1.3;bk.add(bb);
box(3.1,2.1,.1,M(0x2f5d46,.8),0,0,-.09,bb);
function pg(x,ry){const g=new THREE.Group();g.position.set(x,0,0);g.rotation.y=ry;bb.add(g);box(1.5,2,.06,M(0xf6f0df,.9),0,0,-.03,g);return screen(1.42,1.9,0,0,.01,g)}
const bL=pg(-.74,0),bR=pg(.74,0);box(.05,2.05,.03,M(0xc63a2d),0,0,.05,bb);
tag(bk,'riwayat');
let wr=null;
const nNew=()=>S.riw.length-S.sync;
const flt=()=>S.riw.slice(S.riw.length-(wr?S.riw.length:S.sync)).filter(r=>S.f=='all'||(S.f=='set'&&r.v>0)||(S.f=='tar'&&r.v<0));
bL.paint((g,W,H,B)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,H);tx(g,'BUKU TABUNGAN',W/2,60,36,'#3a2f1e','center');tx(g,USER_NAME+' · '+USER_NO,W/2,96,24,'#6b5a3d','center','600');
 tx(g,'Saldo tercatat',40,150,24,'#6b5a3d','left','600');tx(g,rp(S.bs-S.bt),40,206,50,'#1f6b46');tx(g,'Ditahan '+rp(S.bt),40,246,24,'#a0701a','left','600');tx(g,flt().length+' catatan ditampilkan',40,284,22,'#6b5a3d','left','600');
 [['all','Semua'],['set','Setoran'],['tar','Tarikan']].forEach((f,i)=>B(40,312+i*62,432,52,f[1],()=>{if(!S.wrt){S.f=f[0];redraw()}},S.f==f[0]?'pon':'p'));
 B(40,530,432,96,nNew()>0?'Perbarui ('+nNew()+' baru)':'Perbarui buku',S.wrt?null:writeBook,'g');
 tx(g,nNew()>0?'Ada catatan yang belum ditulis di buku':(S.m.bk||'Buku sudah terbaru'),W/2,662,21,nNew()>0?'#a0701a':'#6b5a3d','center','700')});
bR.paint((g,W,H)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,H);tx(g,'Catatan',40,64,32,'#3a2f1e');const L=flt().slice(0,6);if(!L.length)tx(g,'Belum ada catatan.',40,130,24,'#6b5a3d','left','600');
 L.forEach((r,i)=>{const y=120+i*96;if(wr&&i<wr.n&&i>wr.i)return;g.save();if(wr&&i==wr.i&&i<wr.n){g.beginPath();g.rect(0,y-34,40+(W-60)*wr.p,110);g.clip()}
  g.fillStyle='#d9cfb5';g.fillRect(40,y-30,W-80,2);tx(g,r.k+' · '+r.t,40,y+4,24,'#3a2f1e');tx(g,(r.v<0?'−':'+')+Math.abs(Math.round(r.v)).toLocaleString('id-ID'),W-40,y+4,26,r.v<0?'#b03a2d':'#1f6b46','right');tx(g,r.d,40,y+36,21,'#6b5a3d','left','600');g.restore()});tx(g,'Halaman 1',W/2,H-24,20,'#9a8a68','center','600')});
// pensil yang menulis di halaman kanan
const pencil=new THREE.Group();pencil.visible=false;bR.add(pencil);
cyl(.035,.5,M(0xf3c64f,.5),0,.4,0,pencil,8);cyl(.0375,.05,M(0xcfd5da,.3,.8),0,.67,0,pencil,8);cyl(.037,.07,M(0xf6bdb0,.7),0,.72,0,pencil,8);
const ptip=new THREE.Mesh(new THREE.ConeGeometry(.035,.13,10),M(0xe8d3a8,.7));ptip.rotation.x=Math.PI;ptip.position.y=.065;pencil.add(ptip);
const plead=new THREE.Mesh(new THREE.ConeGeometry(.013,.04,8),M(0x26332d));plead.rotation.x=Math.PI;plead.position.y=.02;pencil.add(plead);
pencil.rotation.set(.85,0,-.4);
const pAt=(px,py,lift=0)=>pencil.position.set((px/bR.W-.5)*1.42,(.5-py/bR.H)*1.9,.012+lift);
async function writeBook(){if(S.wrt)return;S.wrt=1;S.f='all';pencil.visible=true;
 const n=Math.min(nNew(),6);
 if(!n){S.m.bk='Buku sudah terbaru';redraw();await tw(p=>pAt(60+p*300,92+Math.sin(p*26)*5,.03*Math.sin(p*3)),900);pencil.visible=false;S.wrt=0;note('Buku tabungan sudah terbaru.');redraw();return}
 wr={n,i:0,p:0};redraw();await tw(p=>pAt(300-p*240,40-p*8,.25*(1-p)),450);
 for(let i=0;i<n;i++){wr.i=i;const y=120+i*96;await tw(p=>{wr.p=p;pAt(44+p*(bR.W-100),y+24+Math.sin(p*46)*6,.012*Math.sin(p*46));pencil.rotation.z=-.4+Math.sin(p*46)*.06;bR.render()},850)}
 wr=null;S.bs=S.saldo;S.bt=S.tahan;S.sync=S.riw.length;S.m.bk='Buku sudah diperbarui';
 await tw(p=>pAt(bR.W-60-p*20,y0End()+0,p*.3),350);pencil.visible=false;S.wrt=0;note('Catatan baru sudah ditulis di buku.');redraw()}
const y0End=()=>140;

// 5 RAK + KIOS
const rk=new THREE.Group();rk.position.set(-7.3,0,0);row.add(rk);
const wood=M(0xc79a63,.7);[0,1.8,3.6].forEach(y=>box(4.6,.18,1.2,wood,0,y+.3,0,rk));[-2.2,2.2].forEach(x=>box(.2,4.2,1.2,wood,x,2.3,0,rk));
function mk(j,x,y){const g=new THREE.Group();g.position.set(x,y,0);rk.add(g);
 if(j=='Plastik')for(let i=0;i<3;i++){const b=cyl(.22,.9,M(0x8fc6ec,.2),i*.55-.55,.5,0,g);b.material.transparent=true;b.material.opacity=.85;cyl(.09,.2,M(0x3f7fb0),i*.55-.55,1.05,0,g)}
 if(j=='Kertas')for(let i=0;i<4;i++)box(1.5,.22,.9,M(i%2?0xd9b27a:0xc79b60),0,.15+i*.23,0,g).rotation.y=i*.05;
 if(j=='Logam')for(let i=0;i<3;i++)cyl(.26,.7,M(0xcdd3d8,.25,.9),i*.6-.6,.4,0,g);
 screen(1.5,.4,0,-.05,.65,g).paint((gg,W,H)=>{bg(gg,W,H,'#fff');tx(gg,j+' '+Math.round(J[j].h/1000)+'rb/kg',W/2,H*.68,40,'#222','center')});
 g.traverse(o=>{o.userData.k='rak';o.userData.j=j});hit.push(g)}
mk('Plastik',-1.1,.4);mk('Kertas',1.1,.4);mk('Logam',0,2.2);
screen(3.4,.6,0,5.2,.2,rk).paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'RAK KATALOG HARGA',W/2,H*.65,44,'#fff','center')});
const kg=new THREE.Group();kg.position.set(3.9,0,.9);rk.add(kg);box(.12,1.2,.12,M(0x26332d),0,.6,-.1,kg);box(2.1,1.75,.12,M(0x26332d),0,1.85,-.08,kg);
const kS=screen(2.0,1.65,0,1.85,0,kg);tag(kg,'rak');
kS.paint((g,W,H,B)=>{bg(g,W,H);tx(g,'Katalog harga · 21 Nov 2026',24,40,22,'#9fd9b8','left','600');Object.keys(J).forEach((k,i)=>B(24+i*160,54,148,56,k,()=>{S.sel=k;redraw()},k==S.sel?'on':'n'));
 const st=S.stok[S.sel],mx=Math.max(.25,Math.min(20,st));if(S.kg>mx)S.kg=mx;
 if(S.m.rak)tx(g,S.m.rak,24,138,20,'#f3c64f','left','600');else if(st<=0)tx(g,'Ambil kantong di Titik Kumpul dulu',24,138,20,'#ff8a7a','left','700');
 tx(g,rp(J[S.sel].h)+'/kg',24,190,50);tx(g,'Stok Ibu: '+fmt(st)+' kg',24,226,26,st>0?'#8ef0be':'#ff8a7a','left','800');tx(g,'Nilai '+rp(S.kg*J[S.sel].h),W-24,226,26,'#f3c64f','right','800');
 B(24,244,76,60,'−',()=>{S.kg=Math.max(.25,S.kg-.25);redraw()});tx(g,fmt(S.kg)+' kg',W/2,288,40,'#fff','center');B(412,244,76,60,'+',()=>{S.kg=Math.min(mx,S.kg+.25);redraw()});
 const ok=st>=S.kg-1e-9&&st>0;B(24,H-84,464,64,ok?'+ Masukkan keranjang':'Stok kosong',ok?()=>{S.stok[S.sel]-=S.kg;S.cart.push({j:S.sel,kg:S.kg});S.tmOff=1e9;S.m.rak=S.sel+' '+fmt(S.kg)+' kg masuk keranjang';S.m.tm='';S.kg=Math.min(S.kg,Math.max(.25,S.stok[S.sel]));redraw()}:null,'g')});

// 6 CAPIT
const cp=new THREE.Group();cp.position.set(8.7,0,0);row.add(cp);
box(3.2,1.4,2.4,cream,0,.7,0,cp);
const gl=new THREE.Mesh(new THREE.BoxGeometry(3,2.6,2.2),new THREE.MeshPhysicalMaterial({color:0xcfeee0,transparent:true,opacity:.2,roughness:.05}));gl.position.y=2.7;cp.add(gl);
[[-1.5,-1.1],[1.5,-1.1],[-1.5,1.1],[1.5,1.1]].forEach(p=>box(.1,2.6,.1,M(0x26332d),p[0],2.7,p[1],cp));box(3.2,.12,2.4,cream,0,4.05,0,cp);
const cpS=screen(2.4,1.2,-.4,.7,1.21,cp,0,800);
const cpL=screen(2.8,.5,0,4.45,1.1,cp);cpL.paint((g,W,H)=>{bg(g,W,H,'#e5594b');tx(g,'MESIN CAPIT PENARIKAN',W/2,H*.66,44,'#fff','center')});
// isi mesin: koin emas dan ikat uang di lantai kaca
const goldM=M(0xf3c64f,.3,.25),bandM=M(0xf3c64f,.5),billM=[M(0x2f7d5b,.6),M(0xf6bdb0,.6),M(0x6fe0a2,.6)];
const tagK=o=>{o.traverse(q=>{q.userData.k='capit'});return o};
function cash(i){const g=new THREE.Group();box(.46,.14,.24,billM[(i||0)%3],0,0,0,g);box(.1,.16,.26,bandM,0,0,0,g);g.userData.h=.14;return tagK(g)}
let sd=7;const rnd=()=>(sd=(sd*16807)%2147483647)/2147483647;
for(let i=0;i<16;i++){const x=-1.3+rnd()*2.6,z=-.95+rnd()*1.7;if(x>.55&&z>.05)continue;const n=1+Math.floor(rnd()*3);for(let j=0;j<n;j++){const c=new THREE.Group();cyl(.13,.035,goldM,0,0,0,c,28);c.position.set(x+rnd()*.05,1.4+.0175+j*.036,z+rnd()*.05);c.rotation.y=rnd()*3;cp.add(tagK(c))}}
const loot=[];[[-.9,-.4],[-.3,.4],[.35,-.45],[-.75,.55],[.1,-.05]].forEach((q,i)=>{const b=cash(i);b.position.set(q[0],1.4+.07,q[1]);b.userData.sx=q[0];b.userData.sz=q[1];b.userData.i=i;cp.add(b);loot.push(b)});
// lubang antar dan nampan hadiah
box(.7,.02,.6,M(0x0b1511),1.05,1.41,.55,cp);
box(.66,.34,.02,M(0x0b1511),1.2,.55,1.215,cp);box(.76,.05,.5,M(0xd4cbb4,.5),1.2,.3,1.45,cp);box(.76,.2,.04,M(0xd4cbb4,.5),1.2,.4,1.7,cp);
// rel, jembatan, troli, kabel, kepala capit
[-1.45,1.45].forEach(x=>box(.08,.08,2.3,M(0x26332d,.4,.6),x,3.82,0,cp));
const bridge=new THREE.Group();bridge.position.set(0,3.82,0);cp.add(bridge);box(2.9,.09,.14,M(0x26332d,.4,.6),0,0,0,bridge);
const trol=new THREE.Group();bridge.add(trol);box(.4,.16,.3,M(0x3a4a42,.4,.6),0,-.04,0,trol);
const cable=cyl(.018,1,M(0x9aa3a8,.3,.9),0,-.5,0,trol,8);
const head=new THREE.Group();trol.add(head);cyl(.12,.16,M(0x5a666d,.3,.8),0,0,0,head);cyl(.07,.1,M(0xf3c64f,.4,.5),0,.12,0,head);
const fg=[];for(let i=0;i<3;i++){const pv=new THREE.Group(),ph=i*2.094;pv.position.set(Math.cos(ph)*.2,-.05,Math.sin(ph)*.2);pv.rotation.y=-ph;head.add(pv);
 box(.2,.04,.05,M(0x5a666d,.3,.8),-.09,.0,0,pv);box(.05,.36,.05,M(0xcfd5da,.3,.9),0,-.18,0,pv);const hk=box(.12,.045,.05,M(0xcfd5da,.3,.9),-.04,-.36,0,pv);hk.rotation.z=-.5;fg.push(pv)}
let H=.5,FA=.25;const setH=h=>{H=h;head.position.y=-h;cable.scale.y=h;cable.position.y=-h/2},setF=a=>{FA=a;fg.forEach(f=>f.rotation.z=a)};setH(.5);setF(.25);
// lampu kelap-kelip di atas kaca
const lamps=[];for(let i=0;i<8;i++){const m=new THREE.MeshBasicMaterial({color:0xf3c64f});const l=new THREE.Mesh(new THREE.SphereGeometry(.07,12,8),m);l.position.set(-1.4+i*.4,4.0,1.23);cp.add(l);lamps.push(m)}
let cpRun=false;function lampTick(){const t=performance.now()/180;lamps.forEach((m,i)=>m.color.set(cpRun?((Math.floor(t)+i)%2?0xf3c64f:0xff8a7a):0xf3c64f));
 const q=trayState(),bl=Math.floor(performance.now()/300)%2;trayLamp.material.color.set(q==2?(bl?0x6fe0a2:0x2f7d5b):q==1?0xff8a7a:0x555555);
 if(cur=='capit'||cur=='papan'){const ph=Math.floor(performance.now()/350);if(ph!==lampTick.ph){lampTick.ph=ph;cpS.render()}}}
let prize=null;
const trayState=()=>{if(!prize)return 0;const t=S.tarik.find(x=>x.id==prize.userData.id);return t&&t.st=='Disetujui'?2:1};
const traySign=screen(.7,.22,1.2,.8,1.222,cp,0,420);
traySign.paint((g,W,H)=>{const q=trayState();bg(g,W,H,q==2?'#2f7d5b':'#26332d');tx(g,q==2?'AMBIL UANG':q==1?'MENUNGGU ADMIN':'NAMPAN UANG',W/2,H*.68,q==1?40:52,q==2?'#fff':'#f3c64f','center','900')});
const trayLamp=new THREE.Mesh(new THREE.SphereGeometry(.05,12,8),new THREE.MeshBasicMaterial({color:0x555555}));trayLamp.position.set(1.52,.55,1.23);cp.add(trayLamp);
const trayHit=new THREE.Mesh(new THREE.BoxGeometry(.95,.75,.9),new THREE.MeshBasicMaterial({transparent:true,opacity:0,depthWrite:false}));trayHit.position.set(1.2,.5,1.45);trayHit.userData.tray=1;cp.add(trayHit);
const bd=new THREE.Group();bd.position.set(3.1,0,.9);cp.add(bd);box(.12,1,.12,M(0x26332d),0,.5,-.1,bd);box(1.5,2.5,.1,M(0x26332d),0,2.1,-.08,bd);
const bS=screen(1.4,2.4,0,2.1,0,bd,0,512);
tag(cp,'capit');
cpS.paint((g,W,H,B)=>{bg(g,W,H,'#1b2a22');const ph=Math.floor(performance.now()/350)%2;
 for(let i=0;i<20;i++){const x=20+i*(W-40)/19;g.fillStyle=(i+ph)%2?'#f3c64f':'#ff8a7a';g.beginPath();g.arc(x,12,6,0,7);g.fill();g.beginPath();g.arc(x,H-12,6,0,7);g.fill()}
 tx(g,'AMBIL UANG',24,66,46,'#f3c64f','left','900');tx(g,'Saldo '+rp(avail()),W-24,66,34,'#8ef0be','right','800');
 B(24,86,130,104,'−',()=>{
  if(S.nom>10000){
   S.nom-=10000;
  }else{
   S.nom=0;
  }
  redraw();
 });
 tx(g,rp(S.nom),W/2,160,62,'#fff','center','900');
 B(W-154,86,130,104,'+',()=>{
  const maxNom=Math.floor(avail()/10000)*10000;
  if(avail()<10000){
   S.m.cp='Saldo belum cukup (minimal Rp 10.000). Setor sampah dulu, Bu.';
   note('Saldo belum cukup untuk penarikan (minimal Rp 10.000). Setor sampah di Timbangan dulu!');
   redraw();
   return;
  }
  if(S.nom+10000>maxNom){
   note('Nominal sudah mencapai batas saldo tersedia ('+rp(maxNom)+').');
   return;
  }
  S.nom+=10000;
  redraw();
 });
 B(24,206,W-48,112,'TARIK UANG',S.busy?null:doCapit,'r');
 tx(g,S.m.cp||(avail()<10000?'Saldo belum cukup. Setor sampah dulu, Bu.':'Admin setuju = capit berhasil. Ditolak = capit gagal.'),W/2,364,26,avail()<10000&&!S.m.cp?'#ff8a7a':'#cfe9da','center','800')});
bS.paint((g,W,H,B)=>{bg(g,W,H,'#12201a');g.fillStyle='#f3c64f';g.fillRect(0,0,W,84);tx(g,'PENGAJUAN TARIK',W/2,58,40,'#2b1f00','center','900');
 if(!S.tarik.length){tx(g,'Belum ada pengajuan',W/2,210,34,'#9fd9b8','center','800');tx(g,'Tekan TARIK UANG di mesin capit',W/2,256,24,'#cfe9da','center','600')}
 S.tarik.slice(0,3).forEach((t,i)=>{const y=104+i*252;g.fillStyle='#ffffff14';rr(g,14,y,W-28,236,22);g.fill();tx(g,t.id,34,y+52,36,'#fff');tx(g,rp(t.v),W-34,y+52,36,'#8ef0be','right');
  g.fillStyle=t.st=='Dicairkan'?'#6fe0a2':t.st=='Disetujui'?'#b9d7f2':t.st=='Ditolak'?'#ff8a7a':'#f3c64f';rr(g,34,y+72,190,42,21);g.fill();tx(g,t.st,129,y+102,25,'#1b211e','center','900');
  const MS={Menunggu:['Capit menjepit uang,','menunggu keputusan Admin'],Disetujui:['Disetujui! Ambil uang di','nampan mesin capit'],Dicairkan:['Selesai, uang sudah','masuk dompet Ibu'],Ditolak:['Capit gagal. Dana','kembali ke saldo']}[t.st]||['',''];
  const mc=t.st=='Ditolak'?'#ff8a7a':t.st=='Dicairkan'?'#6fe0a2':'#eef5f0';tx(g,MS[0],34,y+158,28,mc,'left','800');tx(g,MS[1],34,y+196,28,mc,'left','800')});
 tx(g,S.m.bd||'',24,H-24,24,'#f3c64f','left','700')});

// 7 PINTU GARASI DEPAN (menutup seluruh ruangan)
const dr=new THREE.Group();dr.position.set(0,HH,14.3);sc.add(dr);const NS=9,SH=HH/NS;
for(let i=0;i<NS;i++)box(20.6,SH-.04,.2,M(i%2?0xe9e5da:0xdfdbcf,.6),0,-(i+.5)*SH,0,dr);
box(1.2,.1,.14,M(0x26332d),0,-(HH-.4),.14,dr);
const dsign=screen(10,1.7,0,-HH*.5,.12,dr,0,1024);dsign.paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'BANK SAMPAH SAHABAT IBU',W/2,H*.5,76,'#fff','center');tx(g,'Klik pintu untuk masuk',W/2,H*.8,38,'#cfeedd','center','600')});
[-10.3,10.3].forEach(x=>box(.4,HH+.4,.5,cream,x,HH/2,14.3));box(21.2,.5,.5,cream,0,HH+.2,14.3);
const dl=new THREE.PointLight(0xfff1cc,0,28);dl.position.set(0,3.5,10);sc.add(dl);
function mkSw(x,z,ry,k){const g=new THREE.Group();g.position.set(x,0,z);g.rotation.y=ry;sc.add(g);box(.16,1.3,.16,M(0x26332d),0,.65,0,g);box(1.9,1.4,.14,M(0x26332d),0,1.9,0,g);
 screen(1.7,1.2,0,1.9,.08,g).paint((gg,W,H,B)=>{bg(gg,W,H);tx(gg,'Pintu garasi',24,56,32,'#fff');tx(gg,S.door?'Terbuka':'Tertutup',24,124,48,S.door?'#6fe0a2':'#f3c64f');B(24,H-112,W-48,84,S.door?'Tutup pintu':'Buka pintu',()=>toggleDoor(),S.door?'r':'g')});tag(g,k)}
mkSw(7.5,15.8,0,'luar');mkSw(-6,13.3,Math.PI,'pintu');
tag(dr,'pintu');dr.traverse(o=>o.userData.dr=1);
let doorP=0;
async function toggleDoor(to){if(S.doorBusy)return;S.doorBusy=1;S.door=S.door?0:1;const a=doorP,b=S.door;$('doorBtn').classList.toggle('on',!!S.door);$('ds').textContent=S.door?'buka':'tutup';nav();redraw();
 await tw(p=>{doorP=a+(b-a)*p;dr.scale.y=1-.92*doorP;dl.intensity=doorP*1.3},2200);S.doorBusy=0;
 if(S.door){$('hint').textContent='Seret layar = putar  •  W/S atau joystick = jalan  •  roda mouse atau cubit = zoom  •  ketuk alat = mendekat';hintFade(1);moveTo(to||(cur=='luar'?'home':cur))}else if(cur=='luar'){$('hint').textContent='Garasi masih tertutup. Ketuk pintu atau panel di sampingnya untuk membuka.';hintFade(0)}else{$('hint').textContent='Pintu garasi ditutup. Ketuk tombol Pintu atau panel untuk membuka kembali.';hintFade(1)}}

dekor();
// animasi
const ease=t=>t<.5?2*t*t:1-Math.pow(-2*t+2,2)/2;
const tw=(f,ms)=>new Promise(r=>{const t0=performance.now();(function s(){const p=Math.min(1,(performance.now()-t0)/ms);f(ease(p));p<1?requestAnimationFrame(s):r()})()});
const wait=ms=>new Promise(r=>setTimeout(r,ms));
async function dropItem(j,k){const m=j=='Plastik'?new THREE.Mesh(new THREE.CylinderGeometry(.22,.22,.8,20),M(J[j].c,.3)):j=='Kertas'?new THREE.Mesh(new THREE.BoxGeometry(.9,.4,.7),M(J[j].c)):new THREE.Mesh(new THREE.CylinderGeometry(.28,.28,.6,20),M(J[j].c,.2,.9));m.castShadow=true;m.position.set(0,5,.1);tm.add(m);
 await tw(p=>{m.position.y=5-p*4.3},550);await tw(p=>{m.position.y=.7+Math.sin(p*Math.PI)*.25},260);
 const w0=wShow;await tw(p=>{wShow=w0+k*p;tmS.render()},700);await wait(250);tm.remove(m)}
async function printRcpt(r){if(S.busy||S.tearing)return;S.busy=true;lastR=r;paperOut=false;rcptDraw(r);paper.visible=true;paper.rotation.set(-.25,0,0);paper.position.set(0,.95,.2);paper.scale.y=.01;redraw();await tw(p=>{paper.scale.y=.01+p*.99;paper.position.y=.95+p*.55},1400);paperOut=true;S.busy=false;redraw()}
async function tearRcpt(){if(!paperOut||S.busy||S.tearing)return;S.tearing=1;paperOut=false;redraw();
 await tw(p=>{paper.position.x=Math.sin(p*44)*.05*(1-p);paper.rotation.z=Math.sin(p*34)*.09*(1-p)},380);
 const grp=new THREE.Group();grp.position.copy(paper.position);grp.rotation.set(-.25,0,0);pr.add(grp);paper.visible=false;paper.position.x=0;paper.rotation.z=0;
 const half=top=>{const g=new THREE.PlaneGeometry(1.1,.75),uv=g.attributes.uv;for(let i=0;i<uv.count;i++)uv.setY(i,(top?.5:0)+uv.getY(i)*.5);const m=new THREE.Mesh(g,paper.material);m.position.y=top?.375:-.375;grp.add(m);return m};
 const a=half(true),b=half(false),sc2=[];for(let i=0;i<12;i++){const m=new THREE.Mesh(new THREE.BoxGeometry(.07,.05,.01),new THREE.MeshBasicMaterial({color:0xffffff}));m.position.set((Math.random()-.5)*1.0,.0+Math.random()*.2,0);grp.add(m);sc2.push({m,vx:(Math.random()-.5)*1.6,vy:.6+Math.random()*1.2,r:Math.random()*6})}
 await tw(p=>{a.position.set(p*1.0,.375+p*1.1,0);a.rotation.z=-p*1.2;a.scale.setScalar(1-p*.9);b.position.set(-p*.4,-.375-p*p*2.4,0);b.rotation.z=p*1.0;b.scale.setScalar(1-p*.85);sc2.forEach(o=>{o.m.position.x+=o.vx*.012;o.m.position.y+=(o.vy-p*2.2)*.012;o.m.rotation.z=o.r*p;o.m.scale.setScalar(1-p*.8)})},800);
 pr.remove(grp);S.tearing=0;note('Struk disobek, disimpan Ibu di dompet kertas.');redraw()}
async function doSetor(){if(S.busy||!S.cart.length)return;S.busy=true;S.m.tm='';wShow=0;redraw();moveTo('timbang');await wait(700);
 const rows=[];let tot=0;for(const r of S.cart){await dropItem(r.j,r.kg);const v=r.kg*J[r.j].h;tot+=v;rows.push([r.j+' '+fmt(r.kg)+'kg',Math.round(v).toLocaleString('id-ID')])}
 S.saldo+=tot;if(S.nom<10000&&avail()>=10000)S.nom=10000;const k='SET-'+String(S.n++).padStart(4,'0'),t='21 Nov';S.riw.unshift({k,t,v:tot,d:S.cart.map(r=>r.j+' '+fmt(r.kg)+' kg').join(', ')});S.cart=[];wShow=0;S.m.tm=k+' tercatat, saldo +'+rp(tot);S.busy=false;redraw();
 moveTo('struk');await wait(600);await printRcpt({k,t,rows,v:tot,s:S.saldo})}
const APPROVE=.6;
async function doCapit(){if(S.busy)return;
 if(avail()<10000){S.m.cp='Saldo belum cukup. Setor sampah dulu, Bu.';note('Saldo belum cukup untuk menarik uang.');redraw();return}
 if(S.nom<=0||S.nom>avail()){S.m.cp='Pilih nominal dulu dengan tombol + atau −';redraw();return}
 if(prize||S.tarik.some(t=>t.st=='Menunggu'||t.st=='Disetujui')){S.m.cp='Ambil uang di nampan dulu';note('Ketuk uang di nampan untuk mengambilnya.');redraw();return}
 S.busy=true;S.tahan+=S.nom;const id='TRK-'+String(S.tn++).padStart(4,'0'),v=S.nom,tr={id,v,st:'Menunggu'};S.tarik.unshift(tr);S.m.cp='Capit mengambil uang '+id+'...';S.m.bd='Capit sedang bekerja...';S.nom=Math.min(S.nom,Math.floor(avail()/10000)*10000);redraw();cpRun=true;
 const t=loot.splice(Math.floor(Math.random()*loot.length),1)[0],ex=t.position.x,ez=t.position.z,ty=t.position.y,x0=trol.position.x,z0=bridge.position.z,h0=H,hd=3.82-(t.position.y+.4);
 await tw(p=>setF(FA+(.6-FA)*p),350);
 await tw(p=>{trol.position.x=x0+(ex-x0)*p;bridge.position.z=z0+(ez-z0)*p},1100);
 await tw(p=>setH(h0+(hd-h0)*p),900);
 await tw(p=>setF(.6-.64*p),550);await wait(150);
 cp.remove(t);head.add(t);t.position.set(0,-.36,0);t.rotation.set(0,0,0);
 await tw(p=>setH(hd+(.7-hd)*p),900);
 // capit sudah menjepit uang: tunggu keputusan Admin (acak), hasilnya menentukan capit berhasil atau gagal
 S.m.cp='Capit sudah menjepit uang. Menunggu keputusan Admin...';S.m.bd='Menunggu keputusan Admin...';redraw();
 await tw(p=>{t.rotation.z=Math.sin(p*Math.PI*8)*.07},2800);t.rotation.z=0;
 const ok=Math.random()<APPROVE;
 if(!ok){
  tr.st='Ditolak';S.m.bd='Admin menolak '+id;S.m.cp='Admin menolak. Capit gagal!';redraw();
  const x1=ex+(1.05-ex)*.4,z1=ez+(.55-ez)*.4;
  await tw(p=>{trol.position.x=ex+(x1-ex)*p;bridge.position.z=ez+(z1-ez)*p;t.rotation.z=Math.sin(p*Math.PI*3)*.09},800);
  await tw(p=>setF(-.04+.64*p),300);
  head.remove(t);cp.add(t);t.rotation.set(0,0,0);t.position.set(x1,2.76,z1);
  await tw(p=>{t.position.y=2.76+(ty-2.76)*p*p;t.rotation.z=p*.5},700);t.rotation.z=0;t.position.y=ty;loot.push(t);
  await tw(p=>{trol.position.x=x1*(1-p);bridge.position.z=z1*(1-p);setH(.7+(.5-.7)*p);setF(.6+(.25-.6)*p)},1100);
  S.tahan-=v;S.nom=Math.min(Math.max(S.nom,10000),Math.floor(avail()/10000)*10000);cpRun=false;S.busy=false;S.m.cp='Capit gagal, pengajuan ditolak Admin. Saldo tidak berkurang.';S.m.bd=id+' ditolak. Dana kembali ke saldo.';note('Admin menolak pengajuan, capit gagal. Saldo tidak berkurang.');redraw();return}
 tr.st='Disetujui';S.m.bd='Admin menyetujui '+id;note('Admin menyetujui '+id+'. Capit membawa uang!');redraw();
 await tw(p=>{trol.position.x=ex+(1.05-ex)*p;bridge.position.z=ez+(.55-ez)*p;t.rotation.z=Math.sin(p*Math.PI*3)*.09*(1-p)},1300);
 await tw(p=>setF(-.04+.64*p),350);
 head.remove(t);cp.add(t);t.rotation.set(0,0,0);t.position.set(1.05,3.82-.7-.36,.55);
 await tw(p=>{t.position.y=2.76-1.6*p*p},650);cp.remove(t);
 if(prize)cp.remove(prize);prize=cash(t.userData.i);prize.userData.id=id;prize.traverse(q=>{q.userData.prize=1});prize.position.set(1.2,.5,1.05);cp.add(prize);
 await tw(p=>{prize.position.z=1.05+.42*p;prize.position.y=.5-.15*p},700);
 await Promise.all([tw(p=>{trol.position.x=1.05*(1-p);bridge.position.z=.55*(1-p);setH(.7+(.5-.7)*p);setF(.6+(.25-.6)*p)},1100),
  (async()=>{const n=cash(t.userData.i);n.position.set(t.userData.sx,1.4+.07,t.userData.sz);n.scale.setScalar(.01);n.userData.sx=t.userData.sx;n.userData.sz=t.userData.sz;n.userData.i=t.userData.i;cp.add(n);loot.push(n);await tw(p=>n.scale.setScalar(.01+.99*p),900)})()]);
 cpRun=false;S.busy=false;S.m.cp='Capit berhasil! Ketuk uang di nampan untuk mengambil';S.m.bd=id+' disetujui. Ambil uang di nampan.';redraw()}
async function takeCash(){if(!prize||S.taking)return;const pz=prize;S.taking=1;
 if(trayState()!=2){S.m.cp='Uang belum bisa diambil';note('Tunggu capit selesai dulu ya, Bu.');redraw();const x0=pz.position.x;await tw(p=>{pz.position.x=x0+Math.sin(p*34)*.05*(1-p)},500);pz.position.x=x0;S.taking=0;return}
 const t=S.tarik.find(x=>x.id==pz.userData.id),v=t.v,p0=pz.position.clone(),cs=[];
 for(let i=0;i<16;i++){const c=new THREE.Group();cyl(.11,.03,goldM,0,0,0,c,24);c.position.copy(p0);c.position.y+=.1;cp.add(c);cs.push({c,vx:(Math.random()-.5)*1.8,vy:1.8+Math.random()*1.6,vz:.5+Math.random()*1.6,r:Math.random()*10})}
 await tw(p=>{pz.position.set(p0.x,p0.y+p*1.7,p0.z+p*.8);pz.rotation.y=p*12.5;pz.rotation.x=p*3.2;pz.scale.setScalar(1+.7*Math.sin(p*Math.PI)*(1-p*.4)-p*.6);const tt=p*1.1;cs.forEach(o=>{o.c.position.set(p0.x+o.vx*tt,Math.max(.12,p0.y+.1+o.vy*tt-4.2*tt*tt),p0.z+o.vz*tt);o.c.rotation.set(o.r*p,0,o.r*p*.7);o.c.scale.setScalar(p>.75?1-(p-.75)*4:1)})},1100);
 cp.remove(pz);cs.forEach(o=>cp.remove(o.c));prize=null;S.saldo-=v;S.tahan-=v;t.st='Dicairkan';S.riw.unshift({k:t.id,t:'21 Nov',v:-v,d:'Penarikan tunai'});S.nom=Math.min(S.nom,Math.floor(avail()/10000)*10000);S.m.bd=t.id+' selesai, uang masuk dompet.';S.dompet+=v;S.m.cp='Uang '+rp(v)+' masuk dompet Ibu';note('Uang '+rp(v)+' masuk dompet tunai!');S.taking=0;redraw()}

// kamera
const P={luar:{t:[0,3.2,14.3],r:20,th:0,ph:1.42},home:{t:[0,1.4,-2.5],r:13,th:0,ph:1.3},pintu:{t:[0,2.4,13.4],r:9,th:Math.PI,ph:1.4},scan:{t:[-7.96,1.5,-2.4],r:4.6,th:0,ph:1.2},rak:{t:[-3.0,1.4,-2.2],r:5,th:.12,ph:1.3},timbang:{t:[-.4,1.05,-2.6],r:2.9,th:0,ph:1.38},struk:{t:[1.5,.75,-2.4],r:3,th:-.1,ph:1.3},riwayat:{t:[3.6,1.0,-2.4],r:3.2,th:0,ph:.95},capit:{t:[6.9,1.5,-2.5],r:6.5,th:-.1,ph:1.32},papan:{t:[8.5,1.35,-1.85],r:3.2,th:0,ph:1.45}};
const KEYS=[['home','Semua','#d9d2bf','#a79d84'],['scan','Kumpul','#b9d7f2','#7ea6c8'],['rak','Katalog','#e8c99a','#b8955f'],['timbang','Setoran','#c9ced3','#98a0a8'],['struk','Struk','#f3dc9a','#c4aa5e'],['riwayat','Buku','#bfe6cf','#86b89c'],['capit','Tarik','#f6bdb0','#cc8b7d']];
const ORD=['home','scan','rak','timbang','struk','riwayat','capit'];
const NM={luar:'Luar garasi',home:'Seluruh garasi',pintu:'Pintu garasi',scan:'Titik kumpul warga',rak:'Rak katalog',timbang:'Timbangan',riwayat:'Buku tabungan',capit:'Mesin capit',papan:'Layar pengajuan',struk:'Pencetak struk'};
const cl=o=>JSON.parse(JSON.stringify(o));let G=cl(P.luar),C=cl(P.luar),cur='luar';
$('keys').innerHTML=KEYS.map(n=>`<button data-k="${n[0]}" style="--c:${n[2]};--d:${n[3]}" onclick="moveTo('${n[0]}')">${n[1]}</button>`).join('');
function nav(){document.querySelectorAll('#keys button').forEach(b=>b.classList.toggle('on',b.dataset.k==cur));$('loc').textContent=NM[cur]||'';$('ds2').textContent=S.door?'Pintu terbuka':'Pintu tertutup';document.querySelector('.home').classList.toggle('on',cur=='luar');$('pad').style.display=cur=='luar'?'none':'flex'}
function step(d){let i=ORD.indexOf(cur);i=i<0?0:(i+d+ORD.length)%ORD.length;moveTo(ORD[i])}
async function goStart(){if(cur=='luar')return;if(!S.door)await toggleDoor('luar');else moveTo('luar');await wait(2600);if(S.door&&cur=='luar')toggleDoor()}
function moveTo(k){if(k=='pintu'&&cur=='luar')k='luar';if(cur=='luar'&&k!='luar'&&!S.door){toggleDoor(k);return}cur=k;G=cl(P[k]);nav()}
let drag=null;const pts=new Map(),clampR=r=>Math.max(1.2,Math.min(22,r));let pinch=0;
R.domElement.addEventListener('pointerdown',e=>{drag={x:e.clientX,y:e.clientY,m:false};pts.set(e.pointerId,{x:e.clientX,y:e.clientY});if(pts.size==2){const[a,b]=[...pts.values()];pinch=Math.hypot(a.x-b.x,a.y-b.y);drag.m=true}});
R.domElement.addEventListener('wheel',e=>{if(cur=='luar')return;e.preventDefault();if(cur=='timbang'&&S.tmMax>0){ray.setFromCamera(new THREE.Vector2(e.clientX/innerWidth*2-1,-(e.clientY/innerHeight)*2+1),cam);if(ray.intersectObject(tmS).length){S.tmOff=Math.max(0,Math.min(S.tmMax,(S.tmOff||0)+e.deltaY*.5));redraw();return}}G.r=clampR(G.r*Math.exp(e.deltaY*.0015))},{passive:false});
const kd=new Set(),hold={f:0,b:0,l:0,r:0,zi:0,zo:0},joyV={x:0,y:0};
{const J=$('joy'),K=$('knob');let pid=null;const upd=e=>{const b=J.getBoundingClientRect(),R0=b.width/2-10;let dx=e.clientX-(b.left+b.width/2),dy=e.clientY-(b.top+b.height/2);const d=Math.hypot(dx,dy);if(d>R0){dx*=R0/d;dy*=R0/d}K.style.transform='translate('+dx+'px,'+dy+'px)';const dz=Math.hypot(dx,dy)/R0;joyV.x=dz<.12?0:dx/R0;joyV.y=dz<.12?0:dy/R0};
 const end=()=>{pid=null;joyV.x=joyV.y=0;K.style.transform='';J.classList.remove('on')};
 J.addEventListener('pointerdown',e=>{e.preventDefault();pid=e.pointerId;J.setPointerCapture(pid);J.classList.add('on');upd(e)});
 J.addEventListener('pointermove',e=>{if(e.pointerId===pid)upd(e)});['pointerup','pointercancel','lostpointercapture'].forEach(t=>J.addEventListener(t,e=>{if(e.pointerId===pid||t=='lostpointercapture')end()}))}
addEventListener('keydown',e=>{if(e.ctrlKey||e.metaKey||e.altKey)return;const k=e.key.toLowerCase();if('wasd'.includes(k)&&k.length==1||k.startsWith('arrow')||k=='+'||k=='='||k=='-'){kd.add(k);if(k.startsWith('arrow'))e.preventDefault()}});
addEventListener('keyup',e=>kd.delete(e.key.toLowerCase()));addEventListener('blur',()=>{kd.clear();for(const k in hold)hold[k]=0});
document.querySelectorAll('#pad button').forEach(b=>{const k=b.dataset.h,on=v=>e=>{e.preventDefault();hold[k]=v;b.classList.toggle('h',!!v)};b.addEventListener('pointerdown',on(1));['pointerup','pointerleave','pointercancel'].forEach(t=>b.addEventListener(t,on(0)))});
addEventListener('pointermove',e=>{if(pts.has(e.pointerId))pts.set(e.pointerId,{x:e.clientX,y:e.clientY});if(pts.size>=2){const[a,b]=[...pts.values()],d=Math.hypot(a.x-b.x,a.y-b.y);if(pinch&&d>0&&cur!='luar')G.r=clampR(G.r*pinch/d);pinch=d;return}if(!drag)return;if(Math.abs(e.clientX-drag.x)+Math.abs(e.clientY-drag.y)>4){drag.m=true;$('remote').classList.add('dim')}G.th-=(e.clientX-drag.x)*.006;G.ph=Math.max(.7,Math.min(1.6,G.ph-(e.clientY-drag.y)*.004));drag.x=e.clientX;drag.y=e.clientY});
const ray=new THREE.Raycaster();
addEventListener('pointercancel',e=>{pts.delete(e.pointerId);if(pts.size<2)pinch=0});
addEventListener('pointerup',e=>{pts.delete(e.pointerId);if(pts.size<2)pinch=0;$('remote').classList.remove('dim');if(drag&&!drag.m&&e.target===R.domElement){ray.setFromCamera(new THREE.Vector2(e.clientX/innerWidth*2-1,-(e.clientY/innerHeight)*2+1),cam);
 const hs=ray.intersectObjects(hit,true).filter(x=>{for(let q=x.object;q;q=q.parent)if(q.visible===false)return false;return true});if(hs[0])act(hs[0])}drag=null});
function act(h){const o=h.object,k=o.userData.k;
 if(o.userData.j){S.sel=o.userData.j;redraw()}
 if(o.userData.bag&&cur=='scan')return collectBag(o.userData.bag);
 if(cur=='capit'&&(o.userData.prize||o.userData.tray))return takeCash();
 if(o===paper&&cur=='struk')return tearRcpt();
 const inBoard=cur=='papan'&&o===bS;
 if(o.hot&&(cur==k||inBoard)){const px=h.uv.x*o.W,py=(1-h.uv.y)*o.H,b=o.hot.find(b=>px>=b.x&&px<=b.x+b.w&&py>=b.y&&py<=b.y+b.h);if(b)b.cb();else if(o===bS&&cur=='capit')moveTo('papan')}
 else if(o.userData.dr&&(cur=='pintu'||cur=='luar'))toggleDoor();else moveTo(k)}
function size(){R.setSize(innerWidth,innerHeight);cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix();nav()}addEventListener('resize',size);
function walk(dt){if(cur=='luar'||S.doorBusy)return;const K=k=>kd.has(k),f=(K('w')||K('arrowup')||hold.f?1:0)-(K('s')||K('arrowdown')||hold.b?1:0)-joyV.y,sd=(K('d')||K('arrowright')||hold.r?1:0)-(K('a')||K('arrowleft')||hold.l?1:0)+joyV.x,z=(K('+')||K('=')||hold.zi?1:0)-(K('-')||hold.zo?1:0);
 if(f||sd){const sp=5.5*dt,fx=-Math.sin(G.th),fz=-Math.cos(G.th),rx=Math.cos(G.th),rz=-Math.sin(G.th);G.t[0]+=(fx*f+rx*sd)*sp;G.t[2]+=(fz*f+rz*sd)*sp;
  const asp=innerWidth/innerHeight,mu=asp<1?Math.min(1.6,1.3/asp):1,r=G.r*mu,kk=Math.sin(G.ph),px=G.t[0]+r*kk*Math.sin(G.th),pz=G.t[2]+r*kk*Math.cos(G.th);
  if(px>10)G.t[0]-=px-10;if(px<-10)G.t[0]-=px+10;if(pz>13.3)G.t[2]-=pz-13.3;if(pz<-4.1)G.t[2]-=pz+4.1}
 if(z)G.r=clampR(G.r*Math.exp(-z*dt*1.1))}
let lastT=performance.now();
function loop(){requestAnimationFrame(loop);const nowT=performance.now();walk(Math.min(.05,(nowT-lastT)/1e3));lastT=nowT;['th','ph','r'].forEach(k=>C[k]+=(G[k]-C[k])*.07);for(let i=0;i<3;i++)C.t[i]+=(G.t[i]-C.t[i])*.07;
 const asp=innerWidth/innerHeight,mu=asp<1?Math.min(1.6,1.3/asp):1;if(cur!='luar'){const c=Math.sin(G.ph)*Math.cos(G.th);if(c>.05)G.r=Math.min(G.r,(13.3-G.t[2])/c/mu)}const r=C.r*mu;cam.position.set(C.t[0]+r*Math.sin(C.ph)*Math.sin(C.th),C.t[1]+r*Math.cos(C.ph),C.t[2]+r*Math.sin(C.ph)*Math.cos(C.th));if(cur!='luar'){cam.position.x=Math.max(-10,Math.min(10,cam.position.x));cam.position.y=Math.max(.5,Math.min(HH-.4,cam.position.y));cam.position.z=Math.max(-4,Math.min(13.3,cam.position.z))}cam.lookAt(C.t[0],C.t[1],C.t[2]);
 lampTick();bagTick();R.render(sc,cam)}
rcptDraw(null);size();redraw();nav();loop();
</script></body></html>