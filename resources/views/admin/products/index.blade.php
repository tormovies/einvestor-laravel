@extends('layouts.app')

@section('title', 'Товары - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление товарами</h1>
        <a href="{{ route('admin.products.create') }}" class="btn">+ Создать товар</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию или SKU" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.products.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($products->count() > 0)
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem; width: 35%; max-width: 420px;">Название</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">SKU</th>
                <th style="text-align: right; padding: 0.5rem; font-size: 0.875rem;">Цена</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Файл</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Статус</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Наличие</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Обновлён</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem; font-size: 0.875rem; font-weight: 500; width: 35%; max-width: 420px; word-wrap: break-word; overflow-wrap: break-word;">
                    <a href="{{ route('products.show', $product->slug) }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       style="color: #2563eb; text-decoration: none; font-weight: 500;">
                        {{ $product->name }}
                    </a>
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $product->sku ?: '-' }}</td>
                <td style="padding: 0.5rem; text-align: right; font-size: 0.875rem;">{{ number_format($product->price, 0, ',', ' ') }} ₽</td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($product->files->isNotEmpty())
                    <span style="font-size: 1.25rem;" title="Есть файл(ы) для скачивания ({{ $product->files->count() }})">📦</span>
                    @else
                    <span style="color: #9ca3af;" title="Нет файла">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($product->status === 'publish')
                    <span style="font-size: 1.25rem;" title="Опубликовано">✅</span>
                    @else
                    <span style="font-size: 1.25rem;" title="Черновик">❌</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($product->stock_status === 'in_stock')
                    <span title="В наличии">✓</span>
                    @else
                    <span style="color: #dc2626;" title="Нет в наличии">✗</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $product->updated_at->format('d.m.Y') }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    style="padding: 0.5rem; background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #dc2626;" 
                                    title="Удалить">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Товаров не найдено</p>
    @endif
</div>
@endsection
