<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DigiFeed - TouchMe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <x-navbar/>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Feed -->
            <div class="lg:col-span-2 space-y-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Posts -->
                @foreach($posts as $post)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <!-- Post Header -->
                        <div class="p-4 flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($post->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $post->user->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
                                <form action="{{ route('feed.destroy', $post) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus post ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Post Content -->
                        <div class="px-4 pb-4">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $post->title }}</h2>
                            <p class="text-gray-700 mb-3">{{ Str::limit($post->content, 200) }}</p>
                        </div>

                        <!-- Post Image -->
                        @if($post->image)
                            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover">
                        @endif

                        <!-- Post Actions -->
                        <div class="p-4 border-t flex items-center justify-between">
                            <div class="flex space-x-6">
                                <button onclick="toggleLike({{ $post->id }})" class="like-btn flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition">
                                    <i class="far fa-thumbs-up {{ $post->isLikedBy(auth()->id()) ? 'fas text-blue-600' : '' }}" id="like-icon-{{ $post->id }}"></i>
                                    <span id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
                                </button>
                                <button onclick="toggleComments({{ $post->id }})" class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition">
                                    <i class="far fa-comment"></i>
                                    <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                                </button>
                            </div>
                            <button class="text-gray-600 hover:text-blue-600">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>

                        <!-- Comments Section -->
                        <div id="comments-{{ $post->id }}" class="hidden border-t">
                            <div class="p-4 space-y-4">
                                <!-- Comment Form -->
                                <form onsubmit="postComment(event, {{ $post->id }})" class="flex space-x-3">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" name="content" placeholder="Tulis komentar..." class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </form>

                                <!-- Comments List -->
                                <div id="comments-list-{{ $post->id }}" class="space-y-3">
                                    @foreach($post->comments as $comment)
                                        <div class="flex space-x-3">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                                            </div>
                                            <div class="flex-1 bg-gray-100 rounded-lg px-4 py-2">
                                                <p class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                                <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Create Post Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Start Writing</h3>
                    <p class="text-sm text-gray-600 mb-4">Gunakan tombol Write untuk membuat artikel baru</p>
                    <button onclick="toggleModal()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-pen mr-2"></i>Write
                    </button>
                </div>

                <!-- Popular Articles -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Most Popular Articles</h3>
                    <div class="space-y-4">
                        @foreach($posts->take(3) as $popular)
                            <div class="flex space-x-3">
                                <div class="w-12 h-12 bg-gray-200 rounded flex-shrink-0"></div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $popular->title }}</h4>
                                    <p class="text-xs text-gray-500">{{ $popular->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="postModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Buat Post Baru</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul post...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                            <textarea name="content" rows="6" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis konten post Anda..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar (Opsional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Posting
                            </button>
                            <button type="button" onclick="toggleModal()" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleModal() {
            document.getElementById('postModal').classList.toggle('hidden');
        }

        function toggleComments(postId) {
            document.getElementById('comments-' + postId).classList.toggle('hidden');
        }

        function toggleLike(postId) {
            fetch(`/feed/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                const icon = document.getElementById('like-icon-' + postId);
                const count = document.getElementById('like-count-' + postId);
                
                if (data.liked) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-blue-600');
                } else {
                    icon.classList.remove('fas', 'text-blue-600');
                    icon.classList.add('far');
                }
                
                count.textContent = data.likes_count;
            });
        }

        function postComment(event, postId) {
            event.preventDefault();
            const form = event.target;
            const content = form.content.value;

            if (!content.trim()) return;

            fetch(`/feed/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentsList = document.getElementById('comments-list-' + postId);
                    const newComment = `
                        <div class="flex space-x-3">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                ${data.comment.user.name.substring(0, 2).toUpperCase()}
                            </div>
                            <div class="flex-1 bg-gray-100 rounded-lg px-4 py-2">
                                <p class="text-sm font-semibold text-gray-900">${data.comment.user.name}</p>
                                <p class="text-sm text-gray-700">${data.comment.content}</p>
                                <p class="text-xs text-gray-500 mt-1">Baru saja</p>
                            </div>
                        </div>
                    `;
                    commentsList.insertAdjacentHTML('afterbegin', newComment);
                    document.getElementById('comment-count-' + postId).textContent = data.comments_count;
                    form.reset();
                }
            });
        }
    </script>
</body>
</html>