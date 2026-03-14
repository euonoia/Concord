@extends('admin.hr1.layouts.app')

@section('content')
<style>
    .dash-gradient-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .metric-card {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: none;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 150px;
        height: 150px;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    .metric-card:hover {
        transform: translateY(-5px);
    }
    .metric-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 12px;
    }
    .metric-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a90a0;
        letter-spacing: 0.5px;
    }
    .metric-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1.2;
    }
    .table-card {
        background: #fff;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fcfcfc;
    }
    .recent-table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8a90a0;
        padding: 12px 20px;
        background: #f8f9fa;
        border: none;
    }
    .recent-table td {
        padding: 12px 20px;
        border: none;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="dash-gradient-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="fw-bold mb-1 text-white">Social Recognition</h3>
            <p class="mb-0 text-white" style="opacity: 0.85; font-size: 0.9rem;">Manage appreciation and recognition posts.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #198754 !important;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Recognition Metrics Row --}}
    <div style="display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; margin-bottom: 30px !important; gap: 20px !important;">
        <div class="metric-card" style="border-top: 5px solid #0d6efd; margin: 0 !important;">
            <div class="metric-icon bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-journal-text"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Posts</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $totalPosts }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #dc3545; margin: 0 !important;">
            <div class="metric-icon bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-heart-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Community<br>Likes</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $totalLikes }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #0dcaf0; margin: 0 !important;">
            <div class="metric-icon bg-info bg-opacity-10 text-info" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Comments</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $totalComments }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #ffc107; margin: 0 !important;">
            <div class="metric-icon bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Avg.<br>Engagement</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $engagementRate }}</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Recognition Posts</h6>
            <a href="{{ route('admin.hr1.recognition.create') }}" class="btn btn-sm btn-primary py-1 px-3 rounded-pill shadow-sm fw-bold" style="font-size: 0.75rem;">
                <i class="bi bi-plus-lg me-1"></i> New Recognition
            </a>
        </div>
        <div class="table-responsive">
            <table class="table recent-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Post Info</th>
                        <th>Created By</th>
                        <th>Engagement</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($post->image_path)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $post->image_path) }}" class="rounded-3 shadow-sm border me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="rounded-3 me-3 bg-light d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                                        <i class="bi bi-card-image text-muted fs-4"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark mb-1">{{ $post->title }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $post->content }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="small fw-bold text-dark">{{ $post->admin->username ?? 'Admin' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger border-0 px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-heart-fill me-1"></i>{{ $post->likes_count }}
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-chat-dots-fill me-1"></i>{{ $post->comments_count }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small fw-semibold">{{ $post->created_at->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $post->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.hr1.recognition.edit', $post->id) }}" 
                                   class="btn btn-sm btn-light border shadow-sm rounded-2 py-1 px-2 text-primary" 
                                   title="Edit Recognition">
                                    <i class="bi bi-pencil-square" style="font-size: 0.9rem;"></i>
                                </a>
                                <form action="{{ route('admin.hr1.recognition.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this recognition post?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm rounded-2 py-1 px-2 text-danger" title="Delete Recognition">
                                        <i class="bi bi-trash3-fill" style="font-size: 0.9rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No recognition posts found. Start by creating one!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
