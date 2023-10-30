@php use App\ValueObjects\Enums\PlanetXIconEnum; @endphp
<label {{ $attributes }}>
    @includeWhen($icon === PlanetXIconEnum::ASTEROID, 'components.planetXIcons.asteroid-icon')
    @includeWhen($icon === PlanetXIconEnum::EMPTY_SPACE, 'components.planetXIcons.empty-space-icon')
    @includeWhen($icon === PlanetXIconEnum::GALAXY, 'components.planetXIcons.galaxy-icon')
    @includeWhen($icon === PlanetXIconEnum::MOON, 'components.planetXIcons.moon-icon')
    @includeWhen($icon === PlanetXIconEnum::PLANET, 'components.planetXIcons.planet-icon')
    @includeWhen($icon === PlanetXIconEnum::PLANET_X, 'components.planetXIcons.planet-x-icon')
</label>
