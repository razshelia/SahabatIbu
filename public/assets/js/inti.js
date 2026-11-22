if(!window.THREE)document.body.insertAdjacentHTML('beforeend','<div style="position:fixed;z-index:20;left:16px;right:16px;bottom:16px;padding:14px 18px;border-radius:16px;background:#3a2a10;color:#fff;font:700 15px sans-serif;text-align:center">Tampilan 3D gagal dimuat. Periksa koneksi internet, lalu <a href="" style="color:#f3c64f">muat ulang halaman</a>.</div>')
const rp=n=>'Rp '+Math.round(n).toLocaleString('id-ID');
const fmt=k=>k.toFixed(2).replace('.',',');
const $=id=>document.getElementById(id);
const cl=(x,a=0,b=1)=>Math.max(a,Math.min(b,x)),sm=t=>t*t*(3-2*t),lerp=(a,b,t)=>a+(b-a)*t;
const J={Plastik:{h:3000,c:0x6fb4e6,d:'Botol PET bersih, tanpa tutup & label'},Kertas:{h:2000,c:0xd9b27a,d:'Kertas & kardus kering, diikat rapi'},Logam:{h:5000,c:0xaab3bb,d:'Kaleng & besi, sisa isi dibersihkan'}};
const S={saldo:187500,tahan:0,riw:[{k:'SET-0181',t:'14 Nov',v:16000,d:'Kertas 8,00 kg'},{k:'SET-0176',t:'7 Nov',v:9000,d:'Logam 1,80 kg'}],cart:[],tarik:[],n:188,tn:22,sel:'Plastik',kg:1,nom:50000,busy:false,f:'all',m:{},door:false,tab:'ambil'};
const avail=()=>S.saldo-S.tahan;
let lastR=null,wShow=0,boost=0,toastT;
function toast(m){const t=$('toast');t.textContent=m;t.classList.add('on');clearTimeout(toastT);toastT=setTimeout(()=>t.classList.remove('on'),3800)}

const R=new THREE.WebGLRenderer({antialias:true});R.setPixelRatio(Math.min(devicePixelRatio,2));R.shadowMap.enabled=true;R.shadowMap.type=THREE.PCFSoftShadowMap;
document.body.prepend(R.domElement);
const sc=new THREE.Scene();sc.background=new THREE.Color(0x7d9bab);sc.fog=new THREE.Fog(0x7d9bab,34,76);
const cam=new THREE.PerspectiveCamera(42,1,.1,70);
sc.add(new THREE.HemisphereLight(0xfff3dc,0x4a4a40,.85));
const sun=new THREE.SpotLight(0xfff0d0,1.3,44,1.05,.5);sun.position.set(0,12,6);sun.castShadow=true;sun.shadow.mapSize.set(1536,1536);sc.add(sun);
const M=(c,r=.6,m=0)=>new THREE.MeshStandardMaterial({color:c,roughness:r,metalness:m});
function box(w,h,d,mat,x,y,z,p){const m=new THREE.Mesh(new THREE.BoxGeometry(w,h,d),mat);m.position.set(x,y,z);m.castShadow=m.receiveShadow=true;(p||sc).add(m);return m}
function cyl(r,h,mat,x,y,z,p,s=24){const m=new THREE.Mesh(new THREE.CylinderGeometry(r,r,h,s),mat);m.position.set(x,y,z);m.castShadow=true;(p||sc).add(m);return m}
const rr=(g,x,y,w,h,r)=>{g.beginPath();g.roundRect?g.roundRect(x,y,w,h,r):g.rect(x,y,w,h)};
const tx=(g,s,x,y,sz=34,col='#8ef0be',a='left',wt='800')=>{g.fillStyle=col;g.font=wt+' '+sz+'px Trebuchet MS';g.textAlign=a;g.fillText(s,x,y)};
const bg=(g,W,H,c='#12201a')=>{const q=g.createLinearGradient(0,0,0,H);q.addColorStop(0,c);q.addColorStop(1,'#0b1511');g.fillStyle=q;g.fillRect(0,0,W,H)};
const SC=[];
function screen(w,h,x,y,z,p,ry=0,px=512){const c=document.createElement('canvas');const L=px,LH=Math.round(px*h/w);c.width=L*2;c.height=LH*2;const g=c.getContext('2d');const t=new THREE.CanvasTexture(c);t.anisotropy=8;const m=new THREE.Mesh(new THREE.PlaneGeometry(w,h),new THREE.MeshBasicMaterial({map:t}));m.position.set(x,y,z);m.rotation.y=ry;(p||sc).add(m);m.hot=[];m.W=L;m.H=LH;
 const B=(bx,by,bw,bh,l,cb,k='n')=>{const C={g:['#6fe0a2','#0d2a1b'],r:['#ff8a7a','#3a0f09'],y:['#f3c64f','#2b1f00'],n:['#2c3b34','#eef5f0'],on:['#eef5f0','#16201b'],p:['#e3d7bd','#3a2f1e'],pon:['#2f7d5b','#ffffff']}[k];g.globalAlpha=cb?1:.35;g.fillStyle=C[0];rr(g,bx,by,bw,bh,bh*.28);g.fill();g.fillStyle=C[1];g.font='800 '+Math.round(bh*.42)+'px Trebuchet MS';g.textAlign='center';g.fillText(l,bx+bw/2,by+bh*.64);g.globalAlpha=1;if(cb)m.hot.push({x:bx,y:by,w:bw,h:bh,cb})};
 m.paint=f=>{m.draw=f;m.render()};m.render=()=>{if(!m.draw)return;m.hot=[];g.setTransform(2,0,0,2,0,0);m.draw(g,L,LH,B);t.needsUpdate=true};SC.push(m);return m}
const hit=[],LB=[],HD=[],RG=[],PILES={};
const tag=(g,k)=>{g.traverse(o=>{o.userData.k=k});hit.push(g)};
const redraw=()=>{SC.forEach(s=>s.render());const b=$('balv');if(b){b.textContent=rp(avail());b.classList.toggle('sk',S.busy)}};
// label melayang di atas alat, penunjuk "tekan di sini", dan lingkaran lantai
function lab(p,x,y,z,n,txt,wd,k){const c=document.createElement('canvas');c.width=640;c.height=160;const g=c.getContext('2d');g.fillStyle='#2f7d5b';rr(g,6,6,628,148,74);g.fill();g.lineWidth=6;g.strokeStyle='#ffffff';g.stroke();g.fillStyle='#f3c64f';g.beginPath();g.arc(80,80,52,0,7);g.fill();tx(g,n,80,104,68,'#2b1f00','center');tx(g,txt,372,102,58,'#fff','center');
 const sp=new THREE.Sprite(new THREE.SpriteMaterial({map:new THREE.CanvasTexture(c),transparent:true,depthTest:false}));sp.scale.set(wd,wd/4,1);sp.position.set(x,y,z);sp.renderOrder=8;p.add(sp);LB.push({sp,k,w:wd})}
function handOn(m,px,py,k,s=1,cond){const c=document.createElement('canvas');c.width=256;c.height=170;const g=c.getContext('2d');g.fillStyle='#f3c64f';rr(g,8,104,240,56,28);g.fill();g.fillStyle='#2b1f00';g.font='800 30px Trebuchet MS';g.textAlign='center';g.fillText('Tekan di sini',128,142);g.save();g.translate(106,6);g.beginPath();[[0,0],[0,64],[17,48],[28,78],[42,72],[31,43],[54,43]].forEach((q,i)=>i?g.lineTo(q[0],q[1]):g.moveTo(q[0],q[1]));g.closePath();g.fillStyle='#fff';g.fill();g.lineWidth=6;g.strokeStyle='#2b1f00';g.lineJoin='round';g.stroke();g.restore();
 const sp=new THREE.Sprite(new THREE.SpriteMaterial({map:new THREE.CanvasTexture(c),depthTest:false,transparent:true}));const gw=m.geometry.parameters.width,gh=m.geometry.parameters.height;sp.sx=.95*s;sp.sy=.63*s;sp.scale.set(sp.sx,sp.sy,1);const y=(.5-py/m.H)*gh-.36*s;sp.position.set((px/m.W-.5)*gw,y,.22);sp.renderOrder=9;sp.k=k;sp.by=y;sp.cond=cond;m.add(sp);HD.push(sp)}
function ring(p,r,k,z=0){const m=new THREE.Mesh(new THREE.RingGeometry(r*.82,r,56),new THREE.MeshBasicMaterial({color:0x6fe0a2,transparent:true,opacity:.15,side:THREE.DoubleSide}));m.rotation.x=-Math.PI/2;m.position.set(0,.03,z);p.add(m);RG.push({m,k})}

