<style>
    .class-access-tile {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 0.75rem;
        text-align: center;
        font-weight: 600;
        padding: 0.75rem 0.9rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
    }

    .class-access-tile--available:hover {
        transform: translateY(-1px);
    }

    .class-access-tile--locked {
        cursor: not-allowed;
    }

    .class-access-tile__label {
        display: block;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .class-access-tile__overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.5rem;
        opacity: 0;
        transform: translateY(6px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        font-size: 0.84rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .class-access-tile--locked:hover .class-access-tile__label {
        opacity: 0.18;
        transform: scale(0.98);
    }

    .class-access-tile--locked:hover .class-access-tile__overlay {
        opacity: 1;
        transform: translateY(0);
    }
</style>
