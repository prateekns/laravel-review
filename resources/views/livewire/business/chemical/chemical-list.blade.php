<div class="chemical-list-box">
    <div class="chemical-list-card-box">
        <div class="chemical-list-card">
            <div class="chemical-list-card-head">
                <div class="titile">
                    <p>{{ __('Chemical List Table') }}</p>
                    <small>{{ __('The chemical ranges are in fact ranges, your ideal/target is editable to what you wish for your company and how your techs manage their pools. If you have the gallons entered for the customer on their profile it will give your techs chemical addition suggestions to the oz for that respective pool!') }}</small>
                </div>
            </div>
            <div class="chemical-list-card-body">
                <div class="table-box table-list-box w-full !mt-[0] !p-[0]">
                    <table class="min-w-full" aria-describedby="Chemical List">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Chemical Name/ Value') }}</th>
                                <th scope="col">{{ __('Range') }}</th>
                                <th scope="col">{{ __('Ideal/ Target') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chemicals as $chemical)
                                <x-business.chemical-row
                                    :name="$chemical->name"
                                    :field="$chemical->id"
                                    :range="$chemical->range"
                                    :value="$businessChemicals->get($chemical->id)->ideal_target"
                                    :editField="$editField"
                                    :editValues="$editValues"
                                    :unit="$chemical->unit"
                                />
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="chemical-list-card-box">
        <div class="chemical-list-card">
            <div class="chemical-list-card-head">
                <div class="titile">
                    <p>Additional maintenance items</p>
                </div>
            </div>
            <div class="chemical-list-card-body">
                <div class="list-box">
                    <ul class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-[24px] w-full"><li>Phosphates (oz)</li><li>Primary Algaecide (oz)</li><li>Algaecide 2 (oz)</li>
                        <li>Stain Remover (oz)</li><li>Metal Remover (oz)</li><li>Clarifier/filter aid (oz)</li><li>Flocculant (oz)</li>
                        <li>Bromine(BR)-ppm; <br>Range: 3-6 ppm</li><li>TDS Value (default 1500ppm)</li><li>Temp Value (°F)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <x-loading :target="'updateChemical,startEdit,cancelEdit'" />
</div>
