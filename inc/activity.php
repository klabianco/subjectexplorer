<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }
    .card h2 {
        color: #007BFF; /* This is a Bootstrap primary color. You can change as needed. */
        font-weight: bold;
    }
    .card-body {
        font-size: 18px;
    }
    .card-body #main-response {
        font-size: 16px;
        color: #333;
    }
    .card-body p {
        color: #666;
    }
    .card-body hr {
        border: 0;
        height: 1px;
        background: #DDD;
    }
</style>


<div class="container">
    <div class="card mt-4">
        <div class="card-body">
            <h2>Your Analysis</h2>
            <div id="main-response"><?php echo $Activity->getResponse(); ?></div>
            <hr>
            <?php if ($Activity->hasGrade()) : ?><p id="grade"><strong>Grade:</strong><?php echo $Activity->getGrade(); ?></p><?php endif; ?>
            <?php if ($Activity->hasSubject()) : ?><p id="subject"><strong>Subject:</strong> <?php echo $Activity->getSubject(); ?></p><?php endif; ?>
            <p id="added-date"><strong>Date Added:</strong> <?php echo $Activity->getFormattedAddedDate(); ?></p>
        </div>
    </div>
</div>