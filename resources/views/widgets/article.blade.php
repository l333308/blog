<div class="container list">
    <div class="row">
        <ul class="list-unstyled col-md-10 offset-md-1">
            @forelse($articles as $article)
            <li class="media">
                @if($article->page_image)
                <a class="media-left mr-3" href="{{ url($article->slug) }}">
                    <img alt="{{ $article->slug }}" src="{{ $article->page_image }}" data-holder-rendered="true">
                </a>
                @endif
                <div class="media-body">
                    <h6 class="media-heading">
                        <a href="{{ url($article->slug) }}">
                            {{ $article->title }}
                        </a>
                    </h6>
                    <div class="meta">
                        <span class="cinema">{{ $article->subtitle }}</span>
                    </div>
                    <div class="description">
                        {{ $article->meta_description }}
                    </div>
                    <div class="extra">
                        @foreach($article->tags as $tag)
                        <a href="{{ url('tag', ['tag' => $tag->tag]) }}">
                            <div class="label"><i class="fas fa-tag"></i>{{ $tag->tag }}</div>
                        </a>
                        @endforeach

                        <div class="info">
                            <i class="fas fa-user"></i>{{ $article->user->name ?? 'null' }}&nbsp;,&nbsp;
                            <i class="fas fa-clock"></i>{{ $article->published_at->diffForHumans() }}&nbsp;,&nbsp;
                            <i class="fas fa-eye"></i>{{ $article->view_count }}
                            <i class="fas fa-comments"></i>{{ $article->comments->count() }}
                            <a href="{{ url($article->slug) }}" class="float-right">
                                Read More <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            @empty
                <h3 class="text-center">{{ lang('Nothing') }}</h3>
            @endforelse
        </ul>
    </div>
</div>

<script type="text/javascript">
    !function(){
        function n(n,e,t){
            return n.getAttribute(e)||t
        }
        function e(n){
            return document.getElementsByTagName(n)
        }
        function t(){
            var t=e("script"),o=t.length,i=t[o-1];
            return{
                l:o,z:n(i,"zIndex",-1),o:n(i,"opacity",.8),c:n(i,"color","156,174,191"),n:n(i,"count",150)
                /*opacity 参数设置的是透明程度，数字越小越透明；  color 设置颜色； count 设置磁线的数量*/
            }
        }
        function o(){
            a=m.width=window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,
                c=m.height=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight
        }
        function i(){
            r.clearRect(0,0,a,c);
            var n,e,t,o,m,l;
            s.forEach(function(i,x){
                for(i.x+=i.xa,i.y+=i.ya,i.xa*=i.x>a||i.x<0?-1:1,i.ya*=i.y>c||i.y<0?-1:1,r.fillRect(i.x-.5,i.y-.5,1,1),e=x+1;e<u.length;e++)n=u[e],
                null!==n.x&&null!==n.y&&(o=i.x-n.x,m=i.y-n.y,
                    l=o*o+m*m,l<n.max&&(n===y&&l>=n.max/2&&(i.x-=.03*o,i.y-=.03*m),
                    t=(n.max-l)/n.max,r.beginPath(),r.lineWidth=t/2,r.strokeStyle="rgba("+d.c+","+(t+.2)+")",r.moveTo(i.x,i.y),r.lineTo(n.x,n.y),r.stroke()))
            }),
                x(i)
        }
        var a,c,u,m=document.createElement("canvas"),
            d=t(),l="c_n"+d.l,r=m.getContext("2d"),
            x=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||
                function(n){
                    window.setTimeout(n,1e3/45)
                },
            w=Math.random,y={x:null,y:null,max:2e4};m.id=l,m.style.cssText="position:fixed;top:0;left:0;z-index:"+d.z+";opacity:"+d.o,e("body")[0].appendChild(m),o(),window.onresize=o,
            window.onmousemove=function(n){
                n=n||window.event,y.x=n.clientX,y.y=n.clientY
            },
            window.onmouseout=function(){
                y.x=null,y.y=null
            };
        for(var s=[],f=0;d.n>f;f++){
            var h=w()*a,g=w()*c,v=2*w()-1,p=2*w()-1;s.push({x:h,y:g,xa:v,ya:p,max:6e3})
        }
        u=s.concat([y]),
            setTimeout(function(){i()},100)
    }();
</script>