// dekor.js: mempercantik garasi 3D agar terasa seperti rumah Ibu.
// Dipakai bersama oleh beranda, halaman setelah login, dan game /garasi.
// Dipanggil sekali setelah ruangan dibuat (ruangan.js / bagian "ruangan" di jelajah).
// Memakai pembantu global: THREE, sc, M, box, cyl, rr, tx, HH. Semua hiasan hanya visual
// (tidak ikut klik) dan diletakkan di tepi ruangan supaya tidak menutupi alat.
function dekor(){
 if(window.__dekorSudah||!window.THREE)return;window.__dekorSudah=1;
 const T=THREE,WX=10.28,BZ=-4.27,PR=[0xff7a8a,0xffd166,0xfff1f1,0xff9f5a,0xc77dff,0xff5d73];
 let sd=11;const rn=()=>(sd=(sd*16807)%2147483647)/2147483647,rg=(a,b)=>a+(b-a)*rn();
 const mat=(c,r=.7,o)=>Object.assign(new T.MeshStandardMaterial({color:c,roughness:r}),o||{});
 const grp=(x,y,z,ry=0,p)=>{const g=new T.Group();g.position.set(x,y,z);g.rotation.y=ry;(p||sc).add(g);return g};
 const kanvas=(w,h,f,rep)=>{const c=document.createElement('canvas');c.width=w;c.height=h;f(c.getContext('2d'),w,h);const t=new T.CanvasTexture(c);t.anisotropy=8;if(rep){t.wrapS=t.wrapT=T.RepeatWrapping;t.repeat.set(rep[0],rep[1])}return t};
 const plane=(w,h,m,x,y,z,p,ry=0)=>{const o=new T.Mesh(new T.PlaneGeometry(w,h),m);o.position.set(x,y,z);o.rotation.y=ry;(p||sc).add(o);return o};
 const bola=(r,m,x,y,z,p,sy=1)=>{const o=new T.Mesh(new T.SphereGeometry(r,16,12),m);o.position.set(x,y,z);o.scale.y=sy;o.castShadow=true;(p||sc).add(o);return o};

 // ---------- tekstur ----------
 const batik=(g,W,H,bgc='#5a3720',fg='#e0a93f',kt='#f6e7c1')=>{g.fillStyle=bgc;g.fillRect(0,0,W,H);const s=W/4;
  for(let i=0;i<4;i++)for(let j=0;j<Math.round(H/s);j++){const cx=i*s+s/2,cy=j*s+s/2;g.strokeStyle=fg;g.lineWidth=s*.045;
   [[0,-1],[1,0],[0,1],[-1,0]].forEach(([dx,dy])=>{g.beginPath();g.ellipse(cx+dx*s*.2,cy+dy*s*.2,s*.2,s*.1,Math.atan2(dy,dx)+Math.PI/2*0,0,7);g.stroke()});
   g.fillStyle=kt;g.beginPath();g.arc(cx,cy,s*.05,0,7);g.fill();
   g.fillStyle=fg;[[0,0],[1,0],[0,1],[1,1]].forEach(([a,b])=>{g.beginPath();g.arc(i*s+a*s,j*s+b*s,s*.035,0,7);g.fill()})}};
 const anyam=(g,W,H,c1='#b98a52',c2='#8f6532')=>{g.fillStyle=c1;g.fillRect(0,0,W,H);g.strokeStyle=c2;g.lineWidth=3;for(let i=-H;i<W+H;i+=12){g.beginPath();g.moveTo(i,0);g.lineTo(i+H,H);g.stroke();g.beginPath();g.moveTo(i,H);g.lineTo(i+H,0);g.stroke()}};
 const langit=(g,W,H)=>{const q=g.createLinearGradient(0,0,0,H);q.addColorStop(0,'#74b3e6');q.addColorStop(.7,'#cfe6f6');q.addColorStop(1,'#eaf4f9');g.fillStyle=q;g.fillRect(0,0,W,H);
  g.fillStyle='#ffffffcc';[[.25,.25,.16],[.62,.18,.2],[.8,.4,.13]].forEach(([a,b,c])=>{[0,.5,1].forEach((k,i)=>{g.beginPath();g.ellipse(W*(a+(i-1)*c*.45),H*b,W*c*.45,H*.07,0,0,7);g.fill()})});
  g.fillStyle='#5c9a58';for(let i=0;i<9;i++){g.beginPath();g.arc(W*(i/8),H*.97,H*(.14+.05*Math.sin(i*2.3)),0,7);g.fill()}
  g.fillStyle='#74b36d';for(let i=0;i<7;i++){g.beginPath();g.arc(W*((i+.5)/7),H*1.02,H*.12,0,7);g.fill()}};

 // ---------- warna ruangan lebih hangat ----------
 const HANGAT={0xcfc9b8:0xf2e7d0,0xc3bdac:0xeadec6,0xb5af9e:0xdccdac,0x8d928b:0xf7f2e8,0x6f7a72:0xb9b2a2};
 sc.traverse(o=>{if(!o.isMesh||!o.material||!o.material.color||!o.geometry.parameters)return;const q=o.geometry.parameters,mx=Math.max(q.width||0,q.height||0,q.depth||0);
  if(mx<HH-.05)return;const h=o.material.color.getHex();if(HANGAT[h]!==undefined)o.material.color.setHex(HANGAT[h])});
 sc.traverse(o=>{if(o.isHemisphereLight){o.color.setHex(0xfff1d8);o.groundColor.setHex(0x7a6850);o.intensity=.95}});

 // ---------- lantai keramik ----------
 {const tk=kanvas(256,256,(g,W,H)=>{g.fillStyle='#b9ad96';g.fillRect(0,0,W,H);const n=2,s=W/n;for(let i=0;i<n;i++)for(let j=0;j<n;j++){const v=176+((i*3+j*5)%4)*4;g.fillStyle=`rgb(${v},${v-8},${v-24})`;g.fillRect(i*s+3,j*s+3,s-6,s-6)}},[9,8.2]);
  const f=plane(20.5,18.8,mat(0xd8d2c4,.9,{map:tk,polygonOffset:true,polygonOffsetFactor:-2,polygonOffsetUnits:-2}),0,.004,4.8);f.rotation.x=-Math.PI/2;f.receiveShadow=true}

 // ---------- lis bawah dinding (hijau sage) + plint ----------
 const sage=mat(0x9db89a,.85),putih=mat(0xfaf6ec,.6);
 box(20.7,1.2,.06,sage,0,.6,BZ+.03);box(20.7,.07,.1,putih,0,1.22,BZ+.05);box(20.7,.14,.09,putih,0,.07,BZ+.045);
 [-1,1].forEach(s=>{box(.06,1.2,18.4,sage,s*(WX-.03),.6,4.9);box(.1,.07,18.4,putih,s*(WX-.05),1.22,4.9);box(.09,.14,18.4,putih,s*(WX-.045),.07,4.9)});

 // ---------- jendela + tirai (dua di tiap sisi) ----------
 const langitT=kanvas(256,320,langit);
 const jendela=(g,cx,cy,w,h,tirai)=>{
  plane(w,h,new T.MeshBasicMaterial({map:langitT}),cx,cy,.02,g);
  const f=.11;box(w+.3,f,.14,putih,cx,cy+h/2+f/2,.07,g);box(w+.3,f,.14,putih,cx,cy-h/2-f/2,.07,g);box(f,h,.14,putih,cx-w/2-f/2,cy,.07,g);box(f,h,.14,putih,cx+w/2+f/2,cy,.07,g);
  box(.05,h,.1,putih,cx,cy,.06,g);box(w,.05,.1,putih,cx,cy+.1,.06,g);
  box(w+.6,.07,.34,putih,cx,cy-h/2-.16,.17,g);
  const rod=cyl(.03,w+1.5,mat(0x6b4a2b,.5),cx,cy+h/2+.42,.24,g,10);rod.rotation.z=Math.PI/2;
  [-1,1].forEach(k=>{const tw=.74,top=cy+h/2+.4,tl=h+.95;
   for(let i=0;i<6;i++){const sh=i%2?0xe6a1a0:0xd78a8a;const b=box(tw/6+.01,tl,.05,mat(sh,.95),cx+k*(w/2+.3+(i-2.5)*tw/6*-1*k*-1*0)+k*(w/2+.12-(i)*tw/6*0)-k*0,top-tl/2,.27+(i%2)*.04,g);b.position.x=cx+k*(w/2-.1+(5-i)*tw/6*1)-k*(tw*.0)+k*.0;b.castShadow=false;
    b.position.x=cx+k*(w/2+.45-i*tw/6)}
   // ikat tirai
   box(.08,.1,.14,mat(0xf3c64f,.5),cx+k*(w/2+.1),cy-.1,.32,g)});
  return g};
 [[-1,2.2],[-1,8.3],[1,1.4],[1,6.4]].forEach(([s,z])=>{const g=grp(s*WX,0,z,s>0?-Math.PI/2:Math.PI/2);jendela(g,0,3.55,2.0,2.05);
  // berkas cahaya matahari
  const L=6.5,a=T.MathUtils.degToRad(-51),gr=kanvas(8,64,(c,W,H)=>{const q=c.createLinearGradient(0,0,0,H);q.addColorStop(0,'#fff');q.addColorStop(1,'#000');c.fillStyle=q;c.fillRect(0,0,W,H)});
  const geo=new T.PlaneGeometry(1.9,L);geo.translate(0,-L/2,0);const bm=new T.Mesh(geo,new T.MeshBasicMaterial({color:0xfff0c4,transparent:true,opacity:.2,alphaMap:gr,blending:T.AdditiveBlending,depthWrite:false,side:T.DoubleSide}));
  bm.position.set(0,3.55+.95,.05);bm.rotation.x=a;g.add(bm)});

 // ---------- dinding belakang: poster, jam, kalender, batik gantung, rak tanaman ----------
 const pb=grp(0,0,BZ,0);
 const poster=(x,y,w,h,f)=>{box(w+.16,h+.16,.06,mat(0x7a5434,.5),x,y,.03,pb);plane(w,h,new T.MeshBasicMaterial({map:kanvas(Math.round(w*220),Math.round(h*220),f)}),x,y,.07,pb)};
 const bunga=(g,x,y,r,c,d=7)=>{g.fillStyle=c;for(let i=0;i<d;i++){const a=i/d*6.283;g.beginPath();g.arc(x+Math.cos(a)*r,y+Math.sin(a)*r,r*.7,0,7);g.fill()}g.fillStyle='#ffd166';g.beginPath();g.arc(x,y,r*.55,0,7);g.fill()};
 poster(-6.2,4.35,1.5,1.05,(g,W,H)=>{g.fillStyle='#fdf2d9';g.fillRect(0,0,W,H);g.fillStyle='#2f7d5b';g.font='800 '+H*.16+'px Trebuchet MS';g.textAlign='center';g.fillText('Pilah dari rumah',W/2,H*.2);
  [[.2,.62,'#6fb4e6','Plastik'],[.5,.62,'#d9b27a','Kertas'],[.8,.62,'#aab3bb','Logam']].forEach(([a,b,c,t])=>{g.fillStyle=c;rr(g,W*a-W*.13,H*.36,W*.26,H*.5,14);g.fill();g.fillStyle='#fff';g.font='800 '+H*.1+'px Trebuchet MS';g.fillText(t,W*a,H*.68)})});
 poster(-4.15,4.35,.95,1.05,(g,W,H)=>{g.fillStyle='#f7e1e4';g.fillRect(0,0,W,H);for(let i=0;i<5;i++){g.strokeStyle='#4f8a4f';g.lineWidth=5;g.beginPath();g.moveTo(W*(.2+i*.15),H*.95);g.quadraticCurveTo(W*(.2+i*.15),H*.6,W*(.16+i*.17),H*.4);g.stroke();bunga(g,W*(.16+i*.17),H*.36,W*.06,PR[i].toString(16).padStart(6,'0').replace(/^/,'#'))}
  g.fillStyle='#8a3b4a';g.font='800 '+H*.1+'px Trebuchet MS';g.textAlign='center';g.fillText('Ibu cuan,',W/2,H*.12);g.fillText('bumi sehat',W/2,H*.22)});
 poster(4.3,4.35,1.5,1.05,(g,W,H)=>{g.fillStyle='#e6f2ea';g.fillRect(0,0,W,H);g.fillStyle='#2f7d5b';g.font='800 '+H*.14+'px Trebuchet MS';g.textAlign='center';g.fillText('Rapi di rumah,',W/2,H*.3);g.fillText('cuan di bank sampah',W/2,H*.52);g.fillStyle='#f3c64f';g.beginPath();g.arc(W/2,H*.78,H*.13,0,7);g.fill();g.fillStyle='#7a5a00';g.font='800 '+H*.14+'px Trebuchet MS';g.fillText('Rp',W/2,H*.83)});
 poster(6.4,4.35,.95,1.05,(g,W,H)=>{g.fillStyle='#fff3cc';g.fillRect(0,0,W,H);bunga(g,W*.5,H*.4,W*.14,'#ff7a8a');bunga(g,W*.25,H*.62,W*.09,'#c77dff');bunga(g,W*.75,H*.62,W*.09,'#ff9f5a');g.fillStyle='#8a5a00';g.font='800 '+H*.1+'px Trebuchet MS';g.textAlign='center';g.fillText('Terima kasih,',W/2,H*.88);});
 // jam dinding
 {const c=cyl(.42,.08,mat(0xfffaf0,.5),0,0,0,pb,40);c.rotation.x=Math.PI/2;c.position.set(2.5,4.35,.1);
  plane(.8,.8,new T.MeshBasicMaterial({transparent:true,map:kanvas(256,256,(g,W,H)=>{g.fillStyle='#fffaf0';g.beginPath();g.arc(W/2,H/2,W/2,0,7);g.fill();g.strokeStyle='#5b3a1e';g.lineWidth=8;g.stroke();g.fillStyle='#3a2a18';for(let i=0;i<12;i++){const a=i/12*6.283;g.beginPath();g.arc(W/2+Math.sin(a)*W*.4,H/2-Math.cos(a)*H*.4,i%3?4:8,0,7);g.fill()}
   g.lineCap='round';g.lineWidth=9;g.beginPath();g.moveTo(W/2,H/2);g.lineTo(W/2+Math.sin(.4)*W*.18,H/2-Math.cos(.4)*H*.18);g.stroke();g.lineWidth=6;g.beginPath();g.moveTo(W/2,H/2);g.lineTo(W/2+Math.sin(1.7)*W*.3,H/2-Math.cos(1.7)*H*.3);g.stroke()})}),2.5,4.35,.15,pb)}
 // kain batik gantung
 {const bt=kanvas(256,512,(g,W,H)=>batik(g,W,H));[-8.1,8.1].forEach(x=>{box(1.35,.07,.07,mat(0x6b4a2b,.5),x,5.1,.1,pb);plane(1.2,2.3,new T.MeshStandardMaterial({map:bt,roughness:.9}),x,3.95,.08,pb);[-.55,0,.55].forEach(k=>box(.03,.5,.03,mat(0xe0a93f),x+k,5.05,.1,pb))})}
 // rak apung dengan tanaman, toples, dan buku
 {const rakNama=(x,y)=>{box(2.4,.07,.34,mat(0x8a5a33,.6),x,y,.2,pb);[-.9,.9].forEach(k=>box(.04,.3,.04,mat(0x3a2a18),x+k,y-.18,.1,pb))};rakNama(-8.4,3.3);rakNama(8.4,3.3)}

 // ---------- karpet batik di jalur depan + karpet anyaman bulat ----------
 {const kt=kanvas(256,512,(g,W,H)=>{batik(g,W,H,'#6a2f2a','#e8b25a','#fbeacb');g.strokeStyle='#f3e2bd';g.lineWidth=14;g.strokeRect(7,7,W-14,H-14)});
  const k=plane(3.2,6.4,mat(0xffffff,.95,{map:kt,polygonOffset:true,polygonOffsetFactor:-4,polygonOffsetUnits:-4}),0,.012,10.3);k.rotation.x=-Math.PI/2;k.receiveShadow=true;
  for(let i=-8;i<=8;i++){box(.05,.01,.18,mat(0xf3e2bd),i*.19,.01,13.55);box(.05,.01,.18,mat(0xf3e2bd),i*.19,.01,7.05)}
  const ay=kanvas(256,256,(g,W,H)=>{anyam(g,W,H,'#c9a06a','#a37a45');g.strokeStyle='#7d5a2e';g.lineWidth=10;g.beginPath();g.arc(W/2,H/2,W*.4,0,7);g.stroke()});
  [[-8.4,11.4,1.0],[8.4,12.2,.9]].forEach(([x,z,r])=>{const c=cyl(r,.02,mat(0xffffff,.95,{map:ay}),x,.015,z,null,40);c.castShadow=false;c.receiveShadow=true})}

 // ---------- tanaman (daun & bunga dikumpulkan lalu dibuat satu InstancedMesh) ----------
 const DA=[],BU=[];
 const daun=(x,y,z,sx,sy,sz,rx,ry,rz,c)=>DA.push([x,y,z,sx,sy,sz,rx,ry,rz,c]);
 const kuntum=(x,y,z,r,c)=>BU.push([x,y,z,r,c]);
 const HIJAU=[0x2f7d3f,0x3b9150,0x2a6b38,0x4aa35a,0x246033];
 const pot=(x,y,z,r,h,c=0xc8683c,p)=>{const m=mat(c,.8),a=new T.Mesh(new T.CylinderGeometry(r,r*.72,h,20),m);a.position.set(x,y+h/2,z);a.castShadow=true;(p||sc).add(a);
  const b=new T.Mesh(new T.CylinderGeometry(r*1.08,r*1.08,h*.14,20),m);b.position.set(x,y+h-h*.07,z);(p||sc).add(b);
  const t=new T.Mesh(new T.CylinderGeometry(r*.9,r*.9,.02,20),mat(0x3b2a1c,1));t.position.set(x,y+h*.97,z);(p||sc).add(t)};
 const lidahMertua=(x,z,s=1,y=0)=>{pot(x,y,z,.3*s,.5*s,0xc8683c);for(let i=0;i<11;i++){const a=rg(0,6.28),d=rg(.03,.17)*s,h=rg(.7,1.45)*s,tl=rg(.03,.22);
   daun(x+Math.cos(a)*d,y+.5*s+h/2,z+Math.sin(a)*d,.1*s,h,.035*s,Math.sin(a)*tl,0,-Math.cos(a)*tl,i%3?0x2f7d3f:0x5aa860)}};
 const monstera=(x,z,s=1,y=0,c=0xe8e0d0)=>{pot(x,y,z,.36*s,.58*s,c);for(let i=0;i<7;i++){const a=i/7*6.28+rg(-.3,.3),hh=rg(.7,1.3)*s,out=rg(.25,.6)*s;
   daun(x+Math.cos(a)*out*.4,y+.58*s+hh*.5,z+Math.sin(a)*out*.4,.03*s,hh,.03*s,Math.sin(a)*.3,0,-Math.cos(a)*.3,0x3a6a30);
   const ex=x+Math.cos(a)*out,ez=z+Math.sin(a)*out,ey=y+.58*s+hh;daun(ex,ey,ez,.62*s,.05*s,.46*s,Math.sin(a)*.25,-a,-Math.cos(a)*.25,HIJAU[i%5])}};
 const bunga3=(x,y,z,s=1,n=9)=>{for(let i=0;i<n;i++){const a=rg(0,6.28),d=rg(0,.22)*s,h=rg(.45,.8)*s;daun(x+Math.cos(a)*d*.5,y+h/2,z+Math.sin(a)*d*.5,.016,h,.016,Math.sin(a)*d,0,-Math.cos(a)*d,0x3d8a47);
   kuntum(x+Math.cos(a)*d,y+h,z+Math.sin(a)*d,rg(.06,.1)*s,PR[i%PR.length]);daun(x+Math.cos(a)*d*.6,y+h*.55,z+Math.sin(a)*d*.6,.18,.02,.07,0,a,.3,0x3b9150)}};
 const vas=(x,y,z,s=1,c=0xf3efe6)=>{const a=new T.Mesh(new T.CylinderGeometry(.11*s,.07*s,.3*s,16),mat(c,.3));a.position.set(x,y+.15*s,z);a.castShadow=true;sc.add(a);const b=new T.Mesh(new T.SphereGeometry(.13*s,16,10),mat(c,.3));b.position.set(x,y+.3*s,z);b.scale.y=.6;sc.add(b);bunga3(x,y+.3*s,z,s,8)};
 const semak=(x,z,r,c,y=0,n=34,bg=null)=>{for(let i=0;i<n;i++){const a=rg(0,6.28),d=Math.sqrt(rn())*r,h=rg(.1,r*.9);daun(x+Math.cos(a)*d,y+r*.35+h*.5,z+Math.sin(a)*d*.8,rg(.45,.8)*r*.55,rg(.35,.6)*r*.5,rg(.45,.8)*r*.55,rg(-.3,.3),rg(0,3),rg(-.3,.3),HIJAU[i%5]);if(bg&&i%3==0)kuntum(x+Math.cos(a)*d*1.05,y+r*.35+h*.55+r*.2,z+Math.sin(a)*d*.85+r*.15,rg(.07,.12),bg[i%bg.length])}};
 const keranjang=(x,z,r,h,y=0)=>{const a=new T.Mesh(new T.CylinderGeometry(r,r*.8,h,24,1,true),new T.MeshStandardMaterial({map:kanvas(128,64,(g,W,H)=>anyam(g,W,H)),roughness:.9,side:T.DoubleSide}));a.position.set(x,y+h/2,z);a.castShadow=true;sc.add(a);
  const c=cyl(r*.8,.03,mat(0x8f6532,.9),x,y+.02,z,null,20);c.castShadow=false;return a};

 // sudut ruangan: tanaman besar
 monstera(-9.5,-3.4,1.25);lidahMertua(9.5,-3.4,1.4);monstera(9.55,13.1,1.15,0,0xd9cdb4);lidahMertua(-9.55,13.1,1.35);
 // sepanjang dinding
 lidahMertua(-9.6,.1,1.1);monstera(9.5,-1.2,.95,0,0xf0d9c4);lidahMertua(-9.6,11.1,1);
 // rak dinding belakang: tanaman gantung kecil + toples
 [-8.4,8.4].forEach((x,i)=>{pot(x-.7,3.34,BZ+.2,.16,.24,0xe9d8be);bunga3(x-.7,3.58,BZ+.2,.9,6);pot(x+.7,3.34,BZ+.2,.14,.2,0xc8683c);for(let k=0;k<6;k++)daun(x+.7+rg(-.1,.1),3.58+rg(.0,.2),BZ+.2+rg(-.1,.1),.16,.04,.1,0,rg(0,3),rg(-.5,.5),HIJAU[k%5]);
  const jr=mat(i?0xf3d28a:0xe9a1a1,.35);[0,.3].forEach(k=>{const j=cyl(.12,.26,jr,x+.15+k,3.47,BZ+.2,null,18);cyl(.13,.05,mat(0x6b4a2b,.5),x+.15+k,3.62,BZ+.2,null,18)})});

 // bufet kayu di dinding kiri & kanan: taplak batik, vas bunga, teko, cangkir, toples kue
 const bufet=(s,z0,len)=>{const x=s*(WX-.4),g=grp(x,0,z0,s>0?-Math.PI/2:Math.PI/2);const L=len;
  box(L,.78,.62,mat(0x9c6b3f,.55),0,.45,0,g);box(L+.08,.05,.7,mat(0x6b4a2b,.5),0,.86,0,g);
  [-1,1].forEach(k=>box(.08,.1,.08,mat(0x4a2f18),k*(L/2-.1),.04,.25,g));
  for(let i=0;i<Math.floor(L/1.1);i++){const px=-L/2+.55+i*1.1;box(.9,.55,.03,mat(0x8a5a33,.6),px,.5,.32,g);box(.09,.05,.03,mat(0xe0a93f,.3),px+.3,.5,.34,g)}
  const run=plane(L*.7,.5,new T.MeshStandardMaterial({map:kanvas(256,64,(c,W,H)=>batik(c,W,H*4)),roughness:.95}),0,.8875,0,g);run.rotation.x=-Math.PI/2;run.position.y=.892;
  return g};
 {const L=bufet(-1,5.6,3.4);vas(-9.88,.89,5.6,1.2,0xf6efe0);L.children.length;const g=L;const gx=-1.15;
   const teko=bola(.14,mat(0xf7f3ea,.25),gx,1.03,.0,g,.85);cyl(.04,.1,mat(0xf7f3ea,.25),gx,1.15,0,g,12);box(.1,.04,.04,mat(0xf7f3ea,.25),gx-.16,1.04,0,g);
   [[-.82,.2],[-.82,-.2],[1.0,.22]].forEach(([a,b])=>{cyl(.065,.07,mat(0xfff7e5,.3),gx+a+.6,.93,b,g,14)});
   [[.78,0xf3d28a],[1.1,0xe9a1a1],[1.42,0x9fd1b8]].forEach(([a,c])=>{cyl(.14,.3,mat(c,.3),a,1.02,.08,g,18);cyl(.15,.05,mat(0x6b4a2b,.5),a,1.19,.08,g,18)})}
 {const g=bufet(1,2.6,3.0);vas(9.88,.89,3.1,1.1,0xfdf6e8);
   cyl(.13,.07,mat(0xf7f3ea,.3),-.7,.94,0,g,20);for(let i=0;i<5;i++)bola(.055,mat([0xf2c14e,0xe9824a,0xd94f4f][i%3],.5),-.7+rg(-.06,.06),.99+rg(0,.05),rg(-.05,.05),g);
   lidahMertua(9.88,2.6,.4,.89)}
 // keranjang anyaman berisi botol daur ulang
 keranjang(-9.4,3.0,.34,.55);keranjang(-9.4,3.7,.3,.45);keranjang(9.5,4.9,.33,.5);
 [[-9.4,3.0],[-9.4,3.7],[9.5,4.9]].forEach(([x,z],i)=>{for(let k=0;k<3;k++){const b=new T.Mesh(new T.CylinderGeometry(.06,.06,.4,10),mat([0x9fd1ff,0xbfe8c8,0xffffff][k],.2,{transparent:true,opacity:.75}));b.position.set(x+rg(-.1,.1),.6+.05*i,z+rg(-.1,.1));b.rotation.set(rg(-.2,.2),0,rg(-.2,.2));sc.add(b)}});

 // ---------- lampu gantung rotan + bendera kain ----------
 {const rt=new T.MeshStandardMaterial({map:kanvas(128,64,(g,W,H)=>anyam(g,W,H,'#d0a56a','#a57b43')),roughness:.9,side:T.DoubleSide});
  [[-6.2,9.2],[6.2,9.2],[-7.9,1.2],[7.9,1.2]].forEach(([x,z],i)=>{const hang=1.5+(i>2?.4:0),y=HH-hang;cyl(.012,hang,mat(0x3a2a18),x,HH-hang/2,z,null,6).castShadow=false;
   const d=new T.Mesh(new T.SphereGeometry(.5,28,12,0,6.283,0,1.15),rt);d.position.set(x,y,z);d.scale.set(1,.8,1);sc.add(d);
   const b=new T.Mesh(new T.SphereGeometry(.12,12,10),new T.MeshBasicMaterial({color:0xfff1c8}));b.position.set(x,y-.02,z);sc.add(b)});
  const pl=(x,y,z,k)=>{const l=new T.PointLight(0xffd9a0,k,13,1.6);l.position.set(x,y,z);sc.add(l)};pl(-6.2,HH-1.9,9.2,.12);pl(6.2,HH-1.9,9.2,.12)}
 {const tri=new T.Shape();tri.moveTo(-.19,0);tri.lineTo(.19,0);tri.lineTo(0,-.5);const gb=new T.ShapeGeometry(tri);
  const tali=(z,y0,sag,n)=>{const im=new T.InstancedMesh(gb,new T.MeshStandardMaterial({roughness:.85,side:T.DoubleSide}),n),m4=new T.Matrix4(),col=new T.Color();const pts=[];
   for(let i=0;i<n;i++){const u=i/(n-1),x=-WX+.2+u*(2*WX-.4),y=y0-sag*Math.sin(u*Math.PI);m4.makeTranslation(x,y,z);im.setMatrixAt(i,m4);im.setColorAt(i,col.setHex(PR[i%PR.length]));pts.push(new T.Vector3(x,y+.01,z))}
   im.frustumCulled=false;im.castShadow=false;sc.add(im);const ln=new T.Line(new T.BufferGeometry().setFromPoints(pts),new T.LineBasicMaterial({color:0x6b4a2b}));sc.add(ln)};
  tali(2.4,HH-.7,.4,26)}

 // ---------- bagian luar: langit, rumput, paving, semak, pohon, atap ----------
 {sc.background=kanvas(8,256,(g,W,H)=>{const q=g.createLinearGradient(0,0,0,H);q.addColorStop(0,'#5b9fd8');q.addColorStop(.55,'#a9cfe9');q.addColorStop(.85,'#e9f3f8');q.addColorStop(1,'#f4f0e0');g.fillStyle=q;g.fillRect(0,0,W,H)});
  if(sc.fog)sc.fog.color.setHex(0xcfe0ea);
  const rump=kanvas(256,256,(g,W,H)=>{g.fillStyle='#6fae5a';g.fillRect(0,0,W,H);for(let i=0;i<900;i++){g.fillStyle=`rgba(${40+rn()*60},${120+rn()*70},${40+rn()*40},.5)`;g.fillRect(rn()*W,rn()*H,2,6)}},[26,24]);
  [[-1,-1],[1,1]].forEach(([s])=>{const r=plane(40,72,mat(0xffffff,1,{map:rump}),s*(27.4),-.04,48);r.rotation.x=-Math.PI/2;r.receiveShadow=true});
  [[-1],[1]].forEach(([s])=>{const r=plane(30,22,mat(0xffffff,1,{map:rump}),s*25.5,-.04,4.5);r.rotation.x=-Math.PI/2;r.receiveShadow=true});
  const pv=kanvas(128,128,(g,W,H)=>{g.fillStyle='#8c8f8a';g.fillRect(0,0,W,H);for(let j=0;j<8;j++)for(let i=0;i<4;i++){const v=150+Math.floor(rn()*40);g.fillStyle=`rgb(${v},${v-6},${v-14})`;g.fillRect(i*32+(j%2)*16+1,j*16+1,30,14)}},[16,26]);
  const d=plane(14,68,mat(0xffffff,.9,{map:pv}),0,-.035,49);d.rotation.x=-Math.PI/2;d.receiveShadow=true;
  // bedeng bunga + semak di depan fasad
  [[-12.2,17.2],[12.2,17.2],[-14.6,20],[14.8,21.5],[-16.8,16],[17,15.5],[-12.4,2],[12.4,4],[-12.6,10],[12.6,9]].forEach(([x,z],i)=>semak(x,z,rg(1.1,1.7),0,0,40,i%2?[0xff4f7a,0xffd166,0xfff1f1]:[0xff7a8a,0xff9f5a,0xc77dff]));
  [[-8.6,16.2],[-6.9,16.5],[6.0,16.6],[8.8,16.2]].forEach(([x,z],i)=>{box(1.3,.5,.55,mat(0xc8683c,.85),x,.25,z);semak(x,z,.55,0,.42,16,[0xff4f7a,0xffd166,0xfff1f1,0xff7a8a])});
  // pot besar di sisi pintu
  [[-9.8,16.3,0xc8683c],[9.8,16.3,0xe8e0d0]].forEach(([x,z,c],i)=>{pot(x,0,z,.45,.75,c);semak(x,z,.55,0,.75,22,[0xff4f7a,0xffd166])});
  // pohon
  [[-19,12,1.2],[20,10,1.35],[-22,26,1.1],[23,24,1.25]].forEach(([x,z,s])=>{cyl(.28*s,3.6*s,mat(0x6b4a2b,.9),x,1.8*s,z,null,10);for(let k=0;k<26;k++){const a=rg(0,6.28),r=Math.sqrt(rn())*2.2*s;daun(x+Math.cos(a)*r,3.6*s+rg(.2,2.6)*s,z+Math.sin(a)*r,rg(1.3,2.0)*s,rg(1.0,1.6)*s,rg(1.3,2.0)*s,0,0,0,[0x3f8f4b,0x4aa35a,0x2f7d3f,0x5ab066][k%4])}});
  // atap pelana genteng + dinding segitiga
  const gen=kanvas(128,128,(g,W,H)=>{g.fillStyle='#b4553a';g.fillRect(0,0,W,H);for(let j=0;j<8;j++){g.fillStyle=j%2?'#a64a32':'#bd5e42';g.fillRect(0,j*16,W,14);g.fillStyle='#8f3f2a';g.fillRect(0,j*16+14,W,2)}},[10,6]);
  const gm=mat(0xffffff,.85,{map:gen,side:T.DoubleSide}),rl=11.8,rh=3.3,len=21.5;
  [-1,1].forEach(s=>{const sl=Math.hypot(rl,rh),a=Math.atan2(rh,rl);const p=new T.Mesh(new T.BoxGeometry(sl,.18,len),gm);p.position.set(s*rl/2,HH+.45+rh/2,4.7);p.rotation.z=-s*a;p.castShadow=false;sc.add(p)});
  const ge=new T.Shape();ge.moveTo(-10.9,0);ge.lineTo(10.9,0);ge.lineTo(0,rh-.15);const gg=new T.Mesh(new T.ShapeGeometry(ge),mat(0xf5ecd8,.9,{side:T.DoubleSide}));gg.position.set(0,HH+.45,14.62);gg.castShadow=false;sc.add(gg);
  box(23.6,.16,.3,mat(0xfaf6ec,.6),0,HH+.38,14.7).castShadow=false;
  // lubang angin bundar di dinding segitiga
  const lb=cyl(.55,.1,mat(0x7a5434,.6),0,HH+1.55,14.66,null,28);lb.rotation.x=Math.PI/2;lb.castShadow=false;const ln=cyl(.42,.12,mat(0xbfdcef,.2),0,HH+1.55,14.67,null,28);ln.rotation.x=Math.PI/2;ln.castShadow=false}

 // ---------- bangun InstancedMesh daun & bunga ----------
 {const m4=new T.Matrix4(),q=new T.Quaternion(),e=new T.Euler(),p=new T.Vector3(),s=new T.Vector3(),c=new T.Color();
  const dm=new T.InstancedMesh(new T.SphereGeometry(.5,9,7),new T.MeshStandardMaterial({roughness:.75}),DA.length);
  DA.forEach((a,i)=>{e.set(a[6],a[7],a[8]);q.setFromEuler(e);m4.compose(p.set(a[0],a[1],a[2]),q,s.set(a[3],a[4],a[5]));dm.setMatrixAt(i,m4);dm.setColorAt(i,c.setHex(a[9]).offsetHSL(0,0,rg(-.03,.04)))});
  dm.frustumCulled=false;dm.castShadow=false;dm.receiveShadow=true;sc.add(dm);
  const bm=new T.InstancedMesh(new T.SphereGeometry(.5,8,6),new T.MeshStandardMaterial({roughness:.6}),BU.length);
  BU.forEach((a,i)=>{m4.compose(p.set(a[0],a[1],a[2]),q.identity(),s.set(a[3]*2,a[3]*1.5,a[3]*2));bm.setMatrixAt(i,m4);bm.setColorAt(i,c.setHex(a[4]))});
  bm.frustumCulled=false;bm.castShadow=false;sc.add(bm)}
}
