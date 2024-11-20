<?php


namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\LineItem;

class LineItemTest extends TestCase
{
    /**
     * Test that a line item belongs to a category.
     */
    public function test_line_item_belongs_to_category(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $lineItem = LineItem::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $lineItem->category);
        $this->assertEquals($category->id, $lineItem->category->id);
    }

    /**
     * Test that a line item belongs to a user.
     */
    public function test_line_item_belongs_to_user(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $lineItem = LineItem::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(User::class, $lineItem->user);
        $this->assertEquals($user->id, $lineItem->user->id);
    }

    /**
     * Test that a category belongs to a user.
     */
    public function test_category_belongs_to_user(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($user->id, $category->user->id);
    }
}
