<?php
namespace App\Cache\Article;
class  ArticleCache implements ArticleInterface
{
    function CacheDetail($article)
    {
        if (isset ($article->articleOtherInfoReview)) {
            $article->articleOtherInfoReview = $article->articleOtherInfoReview;
        }
        if (isset ($article->articleOtherInfoRecipe)) {
            $article->articleOtherInfoRecipe = $article->articleOtherInfoRecipe;
        }
        if (isset ($article->articleCategory)) {
            $article->articleCategory = $article->articleCategory;
        }
        if (isset ($article->related)) {
            $article->related = $article->related;
        }
        if (isset ($article->articleAdress)) {
            $article->articleAdress = $article->articleAdress;
        }
        if (isset ($article->getWard)) {
            $article->getWard = $article->getWard;
        }
        if (isset ($article->getWard->getWard)) {
            $article->getWard->getWard = $article->getWard->getWard;
        }
        if (isset ($article->getWard->getWard->getDistrict)) {
            $article->getWard->getWard->getDistrict = $article->getWard->getWard->getDistrict;
        }
        if (isset ($article->getWard->getWard->getDistrict->getProvince)) {
            $article->getWard->getWard->getDistrict->getProvince = $article->getWard->getWard->getDistrict->getProvince;
        }
        if (isset ($article->tags)) {
            $article->tags = $article->tags;
        }
        if (isset ($article->getUser)) {
            $article->getUser = $article->getUser;
        }
        if (isset ($article->articleRating)) {
            $article->articleRating = $article->articleRating;
        }
        if (isset ($article->articleView)) {
            $article->articleView = $article->articleView;
        }
        if (isset ($article->articleLike)) {
            $article->articleLike = $article->articleLike;
        }
        if (isset ($article->getIngredients)) {
            $article->getIngredients = $article->getIngredients;
        }
        if (isset ($article->articleComment)) {
            $article->articleComment = $article->articleComment()
                ->where('status', 1)
                ->paginate(5);
        }
    }

    function getById($id)
    {
        //Check Cache
        $article = \App\Models\Article::with(
            'articleCategory',
            'articleSteps',
            'articleLocation',
            'articleGuess',
            'articlegallery',
            'articleOtherInfoReview',
            'articleOtherInfoRecipe',
            'articleFollow',
            'articleLike',
            'tags', 'articleRating', 'articleView', 'articleComment', 'getUser', 'articleAdress', 'related', 'BuiltTopArticle', 'getWardName', 'getWard', 'getIngredients'
        )->whereId($id)->first();
        \Cache::forget('article_' . $id, $article);
        \Cache::put('article_' . $id, $article , 43200 );
        return $article;

    }
}